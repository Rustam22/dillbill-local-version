<?php


namespace console\controllers;

use backend\models\Conversation;
use backend\models\ConversationUsers;
use backend\models\SocketUsers;
use backend\models\TrialConversation;
use backend\models\TrialConversationUsers;
use common\models\User;
use consik\yii2websocket\events\WSClientEvent;
use consik\yii2websocket\WebSocketServer;
use DateInterval;
use DateTime;
use Exception;
use Ratchet\ConnectionInterface;
use Yii;
use yii\db\StaleObjectException;

Yii::$app->setTimeZone('Asia/Baku');


class ConversationServer extends WebSocketServer
{
    private $requestOrigin;

    private $levelLogic = array(
        'beginner' =>           ['beginner'],
        'elementary' =>         ['elementary'],
        'pre-intermediate' =>   ['pre-intermediate'],
        'intermediate' =>       ['intermediate'],
        'upper-intermediate' => ['upper-intermediate'],
        'advanced' =>           ['advanced'],
    );


    public function init()
    {
        parent::init();

        $this->on(self::EVENT_CLIENT_CONNECTED, function(WSClientEvent $e) {
            echo "New connection! ({$e->client->resourceId})\n";
            echo $this->requestOrigin."\n";

            $e->client->name = null;
        });

        $this->on(self::EVENT_CLIENT_DISCONNECTED, function(WSClientEvent $e) {
            if (($model = SocketUsers::findOne(['resourceId' => $e->client->resourceId])) !== null) {
                $model->delete();
            }

            echo "({$e->client->resourceId}) has disconnected! \n";
        });
    }


    protected function getCommand(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, true);
        return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
    }


    protected function makeSecurity($user, $socketApiKey, $client, $action): bool
    {
        if(sha1(Yii::$app->devSet->getDevSet('socketApiKey')) != $socketApiKey) {     // If socket Api key is valid
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'API Key is not valid',
                    'action' => $action
                ])
            );

            return false;
        }

        if($user == null) {    // If user is not found
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'User token is not valid',
                    'action' => $action
                ])
            );

            return false;
        }

        return true;
    }


    protected function getCpBalance($user) {
        $currentDate = date_create(date('Y-m-d'));
        $lastPaymentDate = ($user->userParameters->lpd === null) ? date_create(date('Y-m-d')) : date_create($user->userParameters->lpd);
        $dateDifference = date_diff($currentDate, $lastPaymentDate)->format("%a");
        $dateDifference = ($lastPaymentDate > $currentDate) ? (-1) * $dateDifference : $dateDifference;
        $dateDifference = ($dateDifference < 0 ) ? 0 : $dateDifference;
        $result = $user->userParameters->cpBalance - $dateDifference;

        return ($result < 0) ? 0 : $result;
    }


    public function ping() {
        try {
            Yii::$app->db->createCommand('SELECT 1')->query();
        } catch (\yii\db\Exception $exception) {
            Yii::$app->db->close();
            Yii::$app->db->open();
        }
    }


    public function commandAddToSocket(ConnectionInterface $client, $msg) {

        $this->ping();

        try {
            $request = json_decode($msg, true);
            $request['token'] = Yii::$app->devSet->myDecryption($request['token'], Yii::$app->devSet->getDevSet('socketApiKey'));
            $user = User::findOne(['verification_token' => $request['token']]);
        } catch (Exception $exception) {
            $client->send(
                json_encode([
                    'success' => false,
                    'action' => 'addToSocket',
                    'exception' => $exception->getMessage()
                ])
            );
        }

        if($this->makeSecurity($user, $request['socketApiKey'], $client, 'addToSocket')) {
            $socketUser = new SocketUsers();

            $socketUser->resourceId = $client->resourceId;
            $socketUser->userId = $user->id;
            $socketUser->email = $user->email;
            $socketUser->name = $user->userProfile->name;
            $socketUser->level = $user->userParameters->currentLevel;

            if($socketUser->save()) {
                $client->send(
                    json_encode([
                        'success' => true,
                        'action' => 'addToSocket',
                    ])
                );
            } else {
                $client->send(
                    json_encode([
                        'success' => false,
                        'error' => 'Failed to save user in socket',
                        'action' => 'addToSocket'
                    ])
                );
            }
        }
    }




    /**
     * @throws Exception
     */
    protected function commandReserve(ConnectionInterface $client, $msg): bool
    {
        $clientMessage = json_decode($msg, true);
        $clientMessage['token'] = Yii::$app->devSet->myDecryption($clientMessage['token'], Yii::$app->devSet->getDevSet('socketApiKey'));
        $groupSize = Yii::$app->devSet->getDevSet('conversationGroupSize');
        $user = User::findOne(['verification_token' => $clientMessage['token'] ]);
        $conversation = Conversation::findOne(['id' => $clientMessage['conversation-id'] ]);

        $this->makeSecurity($user, $clientMessage['socketApiKey'], $client, 'reserve');

        if($conversation == null) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'conversation',
                    'error-message' => 'Conversation not found',
                    'action' => 'reserve',
                ])
            );

            return false;
        }

        if($conversation->level != $user->userParameters->currentLevel) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'conversation',
                    'error-message' => 'Level mismatch',
                    'action' => 'reserve',
                ])
            );

            return false;
        }

        if($this->getCpBalance($user) == 0) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'balance',
                    'error-message' => 'Not enough balance',
                    'action' => 'reserve',
                ])
            );

            return false;
        }

        $datetime1 = new DateTime('now 00:00');
        $datetime2 = new DateTime($conversation->date);
        $interval = $datetime1->diff($datetime2);

        if($this->getCpBalance($user) < (int)$interval->format('%a')) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'notEnoughBalance',
                    'error-message' => 'Not enough balance for chosen date (your balance: '.$this->getCpBalance($user).' day)',
                    'action' => 'reserve'
                ])
            );

            return false;
        }

        $currentDateTime = new DateTime('now');
        $userStartTime = new DateTime($user->userParameters->startDate);
        $classDate = new DateTime($conversation->date);
        $classStartDateTime = new DateTime($conversation->date.' '.$conversation->startsAt);
        $reserveBefore = Yii::$app->devSet->getDevSet('reserveBefore');
        ($reserveBefore > 0) ? $classStartDateTime->sub(new DateInterval('PT'.$reserveBefore.'M')) : $classStartDateTime->add(new DateInterval('PT'.(-$reserveBefore).'M'));

        if ($userStartTime > $classDate) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => '',
                    'error-message' => 'Your start date exceeds the start time of classes',
                    'classDate' => $classDate->format('Y-m-d H:i'),
                    'userStartTime' => $userStartTime->format('Y-m-d H:i'),
                    'action' => 'reserve',
                ])
            );

            return false;
        }

        if($currentDateTime > $classStartDateTime) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => '',
                    'error-message' => 'Current time exceeds the class start time',
                    'action' => 'reserve',
                ])
            );

            return false;
        }

        $attendeesAmount = $conversation->getReservedConversationUsers()->asArray()->count();

        if($attendeesAmount == $groupSize) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'groupSize',
                    'error-message' => 'The class is full',
                    'attendeesAmount' => $attendeesAmount,
                    'action' => 'reserve',
                ])
            );

            return false;
        }

        $amountReservedClasses = ConversationUsers::findOne([
            'userId' => $user->id,
            'action' => 'reserve',
            'conversationLevel' => $user->userParameters->currentLevel,
            'conversationDate' => $classDate->format('Y-m-d')
        ]);

        if($amountReservedClasses != null) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'reserveExist',
                    'error-message' => 'User has already booked a course',
                    'action' => 'reserve',
                ])
            );

            return false;
        }

        $conversationUser = new ConversationUsers();

        $conversationUser->userId = $user->id;
        $conversationUser->conversationId = $conversation->id;
        $conversationUser->requestDate = $currentDateTime->format('Y-m-d');
        $conversationUser->requestTime = $currentDateTime->format('H:i');
        $conversationUser->userName = $user->userProfile->name;
        $conversationUser->userEmail = $user->email;
        $conversationUser->conversationLevel = $conversation->level;
        $conversationUser->tutorImage = $conversation->teacher->image;
        $conversationUser->tutorName = $conversation->teacher->teacherName;
        $conversationUser->action = 'reserve';
        $conversationUser->conversationDate = $conversation->date;
        $conversationUser->startsAT = $conversation->startsAt;

        if($conversationUser->save(false)) {
            $socketUsers = SocketUsers::find()->select(['resourceId'])->where(['level' => $user->userParameters->currentLevel])->asArray()->all();

            try {
                if ($user->userParameters->googleCalendar == 'yes') {
                    if ($user->userParameters->calendarGmail != null) {
                        Yii::$app->googleCalendar->addAttendee($conversation->eventId, $user->userParameters->calendarGmail);
                    } else {
                        Yii::$app->googleCalendar->addAttendee($conversation->eventId, $user->email);
                    }

                }
            } catch (Exception $exception) {}

            foreach ($socketUsers as $key => $value) {
                if(array_key_exists($value['resourceId'], $this->myClients)) {
                    $this->myClients[$value['resourceId']]->send(json_encode([
                        'success' => true,
                        'error' => '',
                        'error-message' => '',
                        'action' => 'reserve',
                        'referralToken' => Yii::$app->devSet->myEncryption($user->verification_token, Yii::$app->devSet->getDevSet('socketApiKey')),
                        'reservedConversationId' => $conversation->id,
                        'userId' => $user->id,
                        'bigLetter' => strtoupper(substr($user->username, 0,1)),
                        'color' => ($user->userProfile->color == null) ? '#646E82' : $user->userProfile->color
                    ]));
                }
            }
        } else {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'reserveUser',
                    'error-message' => 'Technical problem (failed to reserve user), contact support',
                    'action' => 'reserve',
                ])
            );
        }

        return true;
    }


    protected function commandEnterRoom(ConnectionInterface $client, $msg) {
        $clientMessage = json_decode($msg, true);
        $clientMessage['token'] = Yii::$app->devSet->myDecryption($clientMessage['token'], Yii::$app->devSet->getDevSet('socketApiKey'));
        $user = User::findOne(['verification_token' => $clientMessage['token'] ]);
        $conversation = Conversation::findOne(['id' => $clientMessage['conversation-id'] ]);

        $this->makeSecurity($user, $clientMessage['socketApiKey'], $client, 'enterRoom');

        if($conversation == null) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'conversation',
                    'error-message' => 'Conversation not found',
                    'action' => 'enterRoom',
                ])
            );

            return false;
        }

        if($conversation->level != $user->userParameters->currentLevel) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'conversation',
                    'error-message' => 'Level mismatch',
                    'action' => 'enterRoom',
                ])
            );

            return false;
        }

        if($this->getCpBalance($user) == 0) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'balance',
                    'error-message' => 'Not enough balance',
                    'action' => 'enterRoom',
                ])
            );

            return false;
        }

        $datetime1 = new DateTime('now 00:00');
        $datetime2 = new DateTime($conversation->date);
        $interval = $datetime1->diff($datetime2);

        if(($this->getCpBalance($user) + 1) < (int)$interval->format('%a')) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'notEnoughBalance',
                    'error-message' => 'Not enough balance for chosen date (your balance: '.$this->getCpBalance($user).' day)',
                    'action' => 'enterRoom'
                ])
            );

            return false;
        }

        $currentDateTime = new DateTime('now');
        $classStartDateTime = new DateTime($conversation->date.' '.$conversation->startsAt);
        $classEndDateTime = new DateTime($conversation->date.' '.$conversation->startsAt);
        $classEndDateTime->add(new DateInterval('PT' . 60 . 'M'));

        $enterRoomBefore = Yii::$app->devSet->getDevSet('enterBefore');
        ($enterRoomBefore > 0) ? $classStartDateTime->sub(new DateInterval('PT'.$enterRoomBefore.'M')) : $classStartDateTime->add(new DateInterval('PT'.(-$enterRoomBefore).'M'));

        if($currentDateTime < $classStartDateTime) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => '',
                    'error-message' => 'It is too early to enter the room',
                    'action' => 'enterRoom',
                ])
            );

            return false;
        }

        if($currentDateTime >= $classEndDateTime) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => '',
                    'error-message' => 'It is too late to enter the room',
                    'action' => 'enterRoom',
                    'currentDateTime' => $currentDateTime->format('Y-m-d H:i'),
                    'classStartDateTime' => $classStartDateTime->format('Y-m-d H:i'),
                    'classEndDateTime' => $classEndDateTime->format('Y-m-d H:i'),
                ])
            );

            return false;
        }

        $isReserved = ConversationUsers::findOne(['action' => 'reserve', 'userId' => $user->id, 'conversationId' => $conversation->id]);

        if($isReserved == null) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'noReserve',
                    'error-message' => 'This class is not reserved',
                    'action' => 'enterRoom',
                ])
            );

            return false;
        }

        $isEnteredRoom = ConversationUsers::findOne(['action' => 'enter', 'userId' => $user->id, 'conversationId' => $conversation->id]);

        if($isEnteredRoom == null) {
            $conversationUser = new ConversationUsers();

            $conversationUser->userId = $user->id;
            $conversationUser->conversationId = $conversation->id;
            $conversationUser->requestDate = $currentDateTime->format('Y-m-d');
            $conversationUser->requestTime = $currentDateTime->format('H:i');
            $conversationUser->userName = $user->userProfile->name;
            $conversationUser->userEmail = $user->email;
            $conversationUser->conversationLevel = $conversation->level;
            $conversationUser->tutorImage = $conversation->teacher->image;
            $conversationUser->tutorName = $conversation->teacher->teacherName;
            $conversationUser->action = 'enter';
            $conversationUser->conversationDate = $conversation->date;
            $conversationUser->startsAT = $conversation->startsAt;

            if($conversationUser->save(false)) {
                $client->send(
                    json_encode([
                        'success' => true,
                        'error' => '',
                        'error-message' => '',
                        'action' => 'enterRoom',
                        'zoom' => $conversation->teacher->teacherZoom,
                        'enteredClassId' => $conversation->id,
                    ])
                );
            } else {
                $client->send(
                    json_encode([
                        'success' => false,
                        'error' => 'saveEnterRoom',
                        'error-message' => 'Technical problem (failed to reserve user), contact support',
                        'action' => 'enterRoom',
                    ])
                );
            }
        } else {
            $client->send(
                json_encode([
                    'success' => true,
                    'error' => '',
                    'error-message' => '',
                    'action' => 'enterRoom',
                    'zoom' => $conversation->teacher->teacherZoom,
                    'enteredClassId' => $conversation->id,
                ])
            );
        }

    }


    /**
     * @throws Exception
     */
    protected function commandTrialEnterRoom(ConnectionInterface $client, $msg) {
        $clientMessage = json_decode($msg, true);
        $clientMessage['token'] = Yii::$app->devSet->myDecryption($clientMessage['token'], Yii::$app->devSet->getDevSet('socketApiKey'));
        $user = User::findOne(['verification_token' => $clientMessage['token'] ]);
        $trialConversation = TrialConversation::findOne(['id' => $clientMessage['conversation-id'] ]);

        $this->makeSecurity($user, $clientMessage['socketApiKey'], $client, 'trialEnterRoom');

        if($trialConversation == null) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'conversation',
                    'error-message' => 'Conversation not found',
                    'action' => 'trialEnterRoom',
                ])
            );

            return false;
        }

        if($user->userParameters->currentLevel != 'empty') {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'conversation',
                    'error-message' => 'The level has already been determined',
                    'action' => 'trialEnterRoom',
                ])
            );

            return false;
        }

        $currentDateTime = new DateTime('now');
        $classStartDateTime = new DateTime($trialConversation->date.' '.$trialConversation->startsAt);
        $classEndDateTime = new DateTime($trialConversation->date.' '.$trialConversation->startsAt);
        $classEndDateTime->add(new DateInterval('PT' . 60 . 'M'));

        $enterRoomBefore = Yii::$app->devSet->getDevSet('enterBefore');
        ($enterRoomBefore > 0) ? $classStartDateTime->sub(new DateInterval('PT'.$enterRoomBefore.'M')) : $classStartDateTime->add(new DateInterval('PT'.(-$enterRoomBefore).'M'));

        if($currentDateTime < $classStartDateTime) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => '',
                    'error-message' => 'It is too early to enter the room',
                    'action' => 'trialEnterRoom',
                ])
            );

            return false;
        }

        if($currentDateTime >= $classEndDateTime) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => '',
                    'error-message' => 'It is too late to enter the room',
                    'action' => 'enterRoom',
                    'currentDateTime' => $currentDateTime->format('Y-m-d H:i'),
                    'classStartDateTime' => $classStartDateTime->format('Y-m-d H:i'),
                    'classEndDateTime' => $classEndDateTime->format('Y-m-d H:i'),
                ])
            );

            return false;
        }

        $isReserved = TrialConversationUsers::findOne(['action' => 'reserve', 'userId' => $user->id, 'trialConversationId' => $trialConversation->id]);

        if($isReserved == null) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'noReserve',
                    'error-message' => 'This class is not reserved',
                    'action' => 'trialEnterRoom',
                ])
            );

            return false;
        }

        $isEnteredRoom = TrialConversationUsers::findOne(['action' => 'enter', 'userId' => $user->id, 'trialConversationId' => $trialConversation->id]);

        if($isEnteredRoom == null) {
            $trialConversationUser = new TrialConversationUsers();

            $trialConversationUser->userId = $user->id;
            $trialConversationUser->trialConversationId = $trialConversation->id;
            $trialConversationUser->requestDate = $currentDateTime->format('Y-m-d');
            $trialConversationUser->requestTime = $currentDateTime->format('H:i');
            $trialConversationUser->userName = $user->userProfile->name;
            $trialConversationUser->userEmail = $user->email;
            $trialConversationUser->conversationLevel = $trialConversation->level;
            $trialConversationUser->tutorImage = $trialConversation->tutor->image;
            $trialConversationUser->tutorName = $trialConversation->tutor->teacherName;
            $trialConversationUser->action = 'enter';
            $trialConversationUser->conversationDate = $trialConversation->date;
            $trialConversationUser->startsAT = $trialConversation->startsAt;

            if($trialConversationUser->save(false)) {
                $client->send(
                    json_encode([
                        'success' => true,
                        'error' => '',
                        'error-message' => '',
                        'action' => 'trialEnterRoom',
                        'zoom' => $trialConversation->tutor->teacherZoom,
                        'enteredClassId' => $trialConversation->id,
                    ])
                );
            } else {
                $client->send(
                    json_encode([
                        'success' => false,
                        'error' => 'saveEnterRoom',
                        'error-message' => 'Technical problem (failed to reserve user), contact support',
                        'action' => 'trialEnterRoom',
                    ])
                );
            }
        } else {
            $client->send(
                json_encode([
                    'success' => true,
                    'error' => '',
                    'error-message' => '',
                    'action' => 'trialEnterRoom',
                    'zoom' => $trialConversation->tutor->teacherZoom,
                    'enteredClassId' => $trialConversation->id,
                ])
            );
        }

    }


    /**
     * @throws StaleObjectException
     * @throws \Throwable
     */
    protected function commandCancel(ConnectionInterface $client, $msg) {
        $clientMessage = json_decode($msg, true);
        $clientMessage['token'] = Yii::$app->devSet->myDecryption($clientMessage['token'], Yii::$app->devSet->getDevSet('socketApiKey'));
        $user = User::findOne(['verification_token' => $clientMessage['token'] ]);
        $conversation = Conversation::findOne(['id' => $clientMessage['conversation-id'] ]);

        $this->makeSecurity($user, $clientMessage['socketApiKey'], $client, 'enterRoom');

        if($conversation == null) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'conversation',
                    'error-message' => 'Conversation not found',
                    'action' => 'cancel',
                ])
            );

            return false;
        }

        if($conversation->level != $user->userParameters->currentLevel) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'conversation',
                    'error-message' => 'Level mismatch',
                    'action' => 'cancel',
                ])
            );

            return false;
        }

        if($this->getCpBalance($user) == 0) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'balance',
                    'error-message' => 'Not enough balance',
                    'action' => 'cancel',
                ])
            );

            return false;
        }

        $currentDateTime = new DateTime('now');
        $classStartDateTime = new DateTime($conversation->date.' '.$conversation->startsAt);
        $enterRoomBefore = Yii::$app->devSet->getDevSet('enterBefore');
        ($enterRoomBefore > 0) ? $classStartDateTime->sub(new DateInterval('PT'.$enterRoomBefore.'M')) : $classStartDateTime->add(new DateInterval('PT'.(-$enterRoomBefore).'M'));

        if($currentDateTime >= $classStartDateTime) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => '',
                    'error-message' => 'It is too late to cancel the class',
                    'action' => 'cancel',
                    'currenDateTime' => $currentDateTime->format('Y-m-d H:i'),
                    'classStartDateTime' => $classStartDateTime->format('Y-m-d H:i'),

                ])
            );

            return false;
        }

        $isReserved = ConversationUsers::findOne(['action' => 'reserve', 'userId' => $user->id, 'conversationId' => $conversation->id]);

        if($isReserved == null) {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'noReserve',
                    'error-message' => 'This class is not reserved',
                    'action' => 'cancel',
                ])
            );

            return false;
        }

        if($isReserved->delete()) {
            $socketUsers = SocketUsers::find()->select(['resourceId'])->where(['level' => $user->userParameters->currentLevel])->asArray()->all();

            try {
                if($user->userParameters->googleCalendar == 'yes') {
                    if($user->userParameters->calendarGmail != null) {
                        Yii::$app->googleCalendar->deleteAttendee($user->userParameters->calendarGmail, $conversation->eventId);
                    } else {
                        Yii::$app->googleCalendar->deleteAttendee($user->email, $conversation->eventId);
                    }
                }
            } catch (Exception $exception) {}

            foreach ($socketUsers as $key => $value) {
                if(array_key_exists($value['resourceId'], $this->myClients)) {
                    $this->myClients[$value['resourceId']]->send(json_encode([
                        'success' => true,
                        'error' => '',
                        'error-message' => '',
                        'action' => 'cancel',
                        'referralToken' => Yii::$app->devSet->myEncryption($user->verification_token, Yii::$app->devSet->getDevSet('socketApiKey')),
                        'canceledConversationId' => $conversation->id,
                        'userId' => $user->id
                    ]));
                }
            }
        } else {
            $client->send(
                json_encode([
                    'success' => false,
                    'error' => 'deleteReserve',
                    'error-message' => 'Technical problem (could not cancel class), contact support',
                    'action' => 'cancel',
                ])
            );
        }
    }

}