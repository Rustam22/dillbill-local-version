<?php


namespace frontend\controllers;


use backend\models\Packets;
use backend\models\PaymentActions;
use backend\models\PremiumCode;
use backend\models\TrialConversation;
use backend\models\TrialConversationUsers;
use common\models\User;
use common\models\UserParameters;
use DateTime;
use Error;
use Stripe\Exception\ApiErrorException;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;


class PaymentController extends AppController {

    public $layout = 'dash';

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'payment-intent', 'promo', 'checkout'],
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'promo', 'payment-intent', 'checkout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    Yii::$app->response->redirect(['user/sign-up']);
                },
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],

        ];
    }



    public function actionCheckout($token): string
    {
        $context = array();
        $context['token'] = $token;

        $this->layout = 'payment';

        return $this->render('checkout', $context);
    }


    /**
     * @throws Exception
     */
    public function actionGenerateCheckout()
    {
        if(Yii::$app->request->isAjax) {
            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post();

                $selectedPriceId = $post['PRICE_ID'];
                $trialLessonId = $post['LESSON_ID'];
                $weekdays = $post['weekdays'];
                $timeRange = $post['timeRange'];

                $user = User::findOne(['id' => Yii::$app->user->id]);
                $userParameters = UserParameters::findOne(['userId' => $user->id]);

                $userParameters->trialLessonId = (TrialConversation::find()->where(['id' => $trialLessonId])->exists()) ? $trialLessonId : null;

                if (($userParameters->currentLevel == 'empty') AND (TrialConversationUsers::find()->where(['userId' => $user->id])->exists())) {
                    return json_encode([
                        'success' => false,
                        'error' => 'User must first complete trial lesson.'
                    ]);
                }

                if ($selectedPriceId != 1 AND $userParameters->currentLevel == 'empty') {
                    return json_encode([
                        'success' => false,
                        'error' => 'User level must be defined'
                    ]);
                }

                if ($selectedPriceId != 1) {    // If not trial packet
                    $availableWeekdays = [1, 2, 3, 4, 5, 6];
                    $timeRanges = ['09:00-12:00', '15:00-18:00', '18:00-21:00', '21:00-00:00', '21:00-23:59', '21:00-24:00'];
                    $lessons = Packets::findOne(['id' => $selectedPriceId])->lesson;
                    $currentDate = new DateTime('now');

                    if ($weekdays == '' OR $timeRange == '') {
                        return json_encode([
                            'success' => false,
                            'error' => 'Weekdays or time range is not selected.',
                        ]);
                    }

                    if (sizeof($weekdays) > $lessons) {
                        return json_encode([
                            'success' => false,
                            'error' => 'Selected packet does not include '.$lessons.' lessons',
                        ]);
                    }

                    foreach ($weekdays as $key => $value) {
                        if (!in_array($value, $availableWeekdays)) {
                            return json_encode([
                                'success' => false,
                                'error' => 'Selected weekdays does not exist',
                            ]);
                        }
                    }

                    if (!in_array($timeRange, $timeRanges)) {
                        return json_encode([
                            'success' => false,
                            'error' => 'Selected time range does not exist',
                        ]);
                    }

                    $userParameters->container = json_encode([
                        'weekdays' => implode('', $weekdays),
                        'availability' => $timeRange
                    ]);
                    //$userParameters->availability = $timeRange;
                    //$userParameters->availabilityLCD = $currentDate->format('Y-m-d');
                }

                $userParameters->selectedPriceId = $selectedPriceId;
                $user->generateEmailVerificationToken();

                if (!$user->save() OR !$userParameters->save(false)) {
                    return json_encode([
                        'success' => false,
                        'error' => 'Database error, please contact support'
                    ]);
                } else {
                    return json_encode([
                        'success' => true,
                        'token' => $user->verification_token,
                        'error' => $post
                    ]);
                }
            }
        }
    }


    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if ($action->id == 'webhook')
            $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * @throws \Exception
     */
    protected function handlePaymentIntentSucceeded($paymentIntent = null)
    {
        if ($paymentIntent == null)
            return http_response_code(200);

        if ($paymentIntent->metadata->for == 'kids') {
            print_r('not for dillbill');
            return false;
        }

        $PROMO_DISCOUNT = 0;
        $PROMO_TYPE = null;
        $currency = $paymentIntent->currency;
        $user = User::findOne(['email' => $paymentIntent->metadata->email]);
        $packet = Packets::findOne(['id' => $paymentIntent->metadata->packetId]);

        if($packet == null) {
            print_r('requiredElements');
        }

        $premiumCode = PremiumCode::findOne(['name' => $paymentIntent->metadata->promoCode, 'packetId' => $packet->id]);

        if($premiumCode != null) {
            if ($premiumCode->used < $premiumCode->nTime) {
                $premiumCode->used += 1;
                $premiumCode->save();
            }

            $PROMO_DISCOUNT = $premiumCode->discount;
            $PROMO_TYPE = $premiumCode->type;
        }

        $userParameters = UserParameters::findOne(['userId' => $user->id]);

        $userParameters->currentPacketId = $packet->id;

        if ($packet->id == 1) {     // If trial
            $trialConversation = TrialConversation::findOne(['id' => $user->userParameters->trialLessonId]);
            $trialConversationUser = new TrialConversationUsers();

            $currentDateTime = new DateTime('now');

            $trialConversationUser->userId = $user->id;
            $trialConversationUser->trialConversationId = $trialConversation->id;
            $trialConversationUser->requestDate = $currentDateTime->format('Y-m-d');
            $trialConversationUser->requestTime = $currentDateTime->format('H:i');
            $trialConversationUser->userName = $user->userProfile->name;
            $trialConversationUser->userEmail = $user->email;
            $trialConversationUser->conversationLevel = $trialConversation->level;
            $trialConversationUser->tutorImage = $trialConversation->tutor->image;
            $trialConversationUser->tutorName = $trialConversation->tutor->teacherName;
            $trialConversationUser->action = 'reserve';
            $trialConversationUser->conversationDate = $trialConversation->date;
            $trialConversationUser->startsAT = $trialConversation->startsAt;

            if ($trialConversationUser->save(false)) {
                if(!Yii::$app->devSet->isLocal()) {    // TM data send
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://tutor.dillbill.com/api/1.1/wf/trial-lesson-tutor-assign',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_HTTPHEADER => array(
                            'Class_id: '.$trialConversation->id,
                            'Class_level: '.$trialConversation->level,
                            'User_email: '.$user->email,
                            'Authorization: Bearer '
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);

                    debug($response);
                }

                if (!Yii::$app->devSet->isLocal()) {
                    $classStartDateTime = new DateTime($trialConversation->date.' '.$trialConversation->startsAt);
                    $hourDiff = round(($classStartDateTime->getTimestamp() - $currentDateTime->getTimestamp()) / 3600, 0);
                    $minuteDiff = round(($classStartDateTime->getTimestamp() - $currentDateTime->getTimestamp()) / 60, 0);
                    $unixTimestamp = new DateTime('now');
                    $unixTimestampMinute = new DateTime('now');

                    $classAlignedDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($trialConversation->date.' '.$unixTimestamp->format('H:i')), $user->userProfile->timezone);
                    $classAlignedStartDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($trialConversation->date.' '.$trialConversation->startsAt), $user->userProfile->timezone);
                    $classAlignedEndDateTime = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone(new DateTime($trialConversation->date.' '.$trialConversation->endsAt), $user->userProfile->timezone);

                    try {
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS =>'{
                            "to": "'.$user->email.'",
                            "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['trialBookApprove'].'",
                            "message_data": {
                                "TimeRange": "'.$classAlignedStartDateTime->format('H:i').'-'.$classAlignedEndDateTime->format('H:i').'",
                                "StartDate": "'.$classAlignedDate->format('d-m-Y').'",
                                "email": "'.$user->email.'",
                                "name": "'.$user->userProfile->name.'"
                            },
                            "identifiers": {
                                "id": "'.$user->id.'"
                            }
                        }',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer ',
                                'Content-Type: application/json'
                            ),
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                    }   catch (Exception $exception) {}

                    if ($hourDiff >= 2) {
                        try {
                            $classDateTime = new DateTime($trialConversation->date.' '.$trialConversation->startsAt);
                            $classDateTime->modify('-2 hour');

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => '{
                                "to": "'.$user->email.'",
                                "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['trialNotificationInTwoHours'].'",
                                "send_at": '.$classDateTime->getTimestamp().',
                                "message_data": {
                                    "name": "'.$user->userProfile->name.'",
                                    "TimeRange": "'.$classAlignedStartDateTime->format('H:i').'-'.$classAlignedEndDateTime->format('H:i').'",
                                    "StartDate": "'.$classAlignedDate->format('d-m-Y').'",
                                    "email": "'.$user->email.'"
                                },
                                "identifiers": {
                                    "id": "'.$user->id.'"
                                }
                            }',
                                CURLOPT_HTTPHEADER => array(
                                    'Authorization: Bearer ',
                                    'Content-Type: application/json'
                                ),
                            ));
                            $response = curl_exec($curl);
                            curl_close($curl);
                            print_r('-2 hour');
                            print_r($response);
                        } catch (Exception $exception) {}
                    }

                    if ($minuteDiff >= 5) {
                        $classDateTime = new DateTime($trialConversation->date.' '.$trialConversation->startsAt);
                        $classDateTime->modify('-5 minute');

                        try {
                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => '{
                                "to": "'.$user->email.'",
                                "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['trialNotificationInFiveMinutes'].'",
                                "send_at": '.$classDateTime->getTimestamp().',
                                "message_data": {
                                    "name": "'.$user->userProfile->name.'",
                                    "TimeRange": "'.$classAlignedStartDateTime->format('H:i').'-'.$classAlignedEndDateTime->format('H:i').'",
                                    "StartDate": "'.$classAlignedDate->format('d-m-Y').'",
                                    "email": "'.$user->email.'"
                                },
                                "identifiers": {
                                    "id": "'.$user->id.'"
                                }
                            }',
                                CURLOPT_HTTPHEADER => array(
                                    'Authorization: Bearer ',
                                    'Content-Type: application/json'
                                ),
                            ));
                            $response = curl_exec($curl);
                            curl_close($curl);
                            print_r('-5 minute');
                            print_r($response);
                        } catch (Exception $exception) {}
                    }
                }
            }


        } else {

            /***---------------------- User Balance Starts ----------------------***/
            $preSchedule = $userParameters->currentSchedule;

            $userParameters->currentSchedule = ($paymentIntent->metadata->weekdays != '_') ? $paymentIntent->metadata->weekdays : $userParameters->currentSchedule;
            $userParameters->availability = ($paymentIntent->metadata->availability != '_') ? $paymentIntent->metadata->availability : $userParameters->availability;
            $userParameters->availabilityLCD = ($paymentIntent->metadata->availability != '_') ? date('Y-m-d') : $userParameters->availability;

            $currentDate = date_create(date('Y-m-d'));
            $lastPaymentDate = ($userParameters->lpd === null) ? date_create(date('Y-m-d')) : date_create($userParameters->lpd);
            $dateDifference = date_diff($currentDate, $lastPaymentDate)->format("%a");
            $dateDifference = ($lastPaymentDate > $currentDate) ? (-1)*$dateDifference : $dateDifference;
            $dateDifference = ($dateDifference < 0 ) ? 0 : $dateDifference;
            $delay = $userParameters->cpBalance = $userParameters->cpBalance - $dateDifference;
            $userParameters->cpBalance = $userParameters->cpBalance - $dateDifference;
            $userParameters->cpBalance = ($userParameters->cpBalance < 0) ? 0 : $userParameters->cpBalance;

            $preCp = $userParameters->cp;
            $userParameters->cp = $userParameters->cp + $packet->period;
            $userParameters->cpBalance = $userParameters->cpBalance + $packet->period;
            $userParameters->lpd = date('Y-m-d');
            /***---------------------- User Balance Ends ----------------------***/


            /***----------- Boarding Starts ------------***/
            if ($delay < 0) {   // One day or more daley
                $currentDate = new DateTime('now');

                $places = Yii::$app->acc->possiblePlaces(
                    [$userParameters->currentLevel],
                    ($userParameters->availability == null) ? '18:00-21:00' : $userParameters->availability,
                    $currentDate->format('Y-m-d')
                );

                $startDate = ($places == 0) ? Yii::$app->acc->calculateStartDate($userParameters->currentSchedule)[1] : $currentDate->format('Y-m-d');

                $userParameters->startDate = $startDate;
                $userParameters->proficiency = 'start-date';
            }

            if (($delay >= 0) and ($preSchedule != $userParameters->currentSchedule)) {
                $currentDate = new DateTime('now');

                $places = Yii::$app->acc->possiblePlaces(
                    [$userParameters->currentLevel],
                    ($userParameters->availability == null) ? '18:00-21:00' : $userParameters->availability,
                    $currentDate->format('Y-m-d')
                );

                $startDate = ($places == 0) ? Yii::$app->acc->calculateStartDate($userParameters->currentSchedule)[1] : $currentDate->format('Y-m-d');

                $userParameters->startDate = $startDate;
                $userParameters->proficiency = 'start-date';
            }

            if (($delay >= 0) and ($preCp == 0)) {   // New monthly payment
                $currentDate = new DateTime('now');

                $places = Yii::$app->acc->possiblePlaces(
                    [$userParameters->currentLevel],
                    ($userParameters->availability == null) ? '18:00-21:00' : $userParameters->availability,
                    $currentDate->format('Y-m-d')
                );

                $startDate = ($places == 0) ? Yii::$app->acc->calculateStartDate($userParameters->currentSchedule)[1] : $currentDate->format('Y-m-d');

                $userParameters->startDate = $startDate;
                $userParameters->proficiency = 'start-date';
            }
            /***--------- Boarding Ends ---------***/
        }

        $userParameters->save(false);

        /***---------------------- Payment Record Starts ----------------------***/
        $paymentAction = new PaymentActions();

        $paymentAction->userId = $userParameters->userId;
        $paymentAction->userName = $user->username;
        $paymentAction->email = $user->email;
        $paymentAction->packetId = $packet->id;
        $paymentAction->packetName = Yii::$app->devSet->getTranslate($packet['nameKeyword']);
        $paymentAction->pricePeriod = $packet->period;
        $paymentAction->priceDiscount = $packet->discountPercent;
        $paymentAction->priceTotal = $packet[$currency];

        // Price Calculation
        $AMOUNT = $packet[$currency] - ($packet[$currency] * $packet->discountPercent) / 100;
        $AMOUNT = $AMOUNT - ($AMOUNT * $PROMO_DISCOUNT) / 100;
        $AMOUNT = intval($AMOUNT * 100) / 100;

        $paymentAction->paidAmount = $AMOUNT.' '.strtoupper($currency);
        $paymentAction->promoCode = ($paymentIntent->metadata->promoCode == '_') ? '' : $paymentIntent->metadata->promoCode;
        $paymentAction->promoType = $PROMO_TYPE;
        $paymentAction->promoDiscount = $PROMO_DISCOUNT;
        $paymentAction->paymentType = 'Stripe';
        $paymentAction->dateTime = date('Y-m-d H:i:s');

        $paymentAction->save(false);
        /***---------------------- Payment Record Ends ----------------------***/

        print_r('success');
    }


    /**
     * @throws \Exception
     */
    public function actionWebhook()
    {
        // This is your test secret API key.
        \Stripe\Stripe::setApiKey(Yii::$app->params[(Yii::$app->devSet->isLocal()) ? 'testStripeSecretKey' : 'stripeSecretKey']);
        // Replace this endpoint secret with your endpoint's unique secret
        // If you are testing with the CLI, find the secret by running 'stripe listen'
        // If you are using an endpoint defined with the API or dashboard, look in your webhook settings
        // at https://dashboard.stripe.com/webhooks
        //$endpoint_secret = 'whsec_something';

        $endpoint_secret = Yii::$app->params[(Yii::$app->devSet->isLocal()) ? 'endpointSecretLocal' : 'endpointSecretGlobal'];

        $payload = @file_get_contents('php://input');
        $event = null;

        //print_r($payload);

        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            echo '⚠️  Webhook error while parsing basic request.';
            http_response_code(400);
            exit();
        }

        if ($endpoint_secret) {
            // Only verify the event if there is an endpoint secret defined
            // Otherwise use the basic decoded event
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sig_header, $endpoint_secret
                );
            } catch(\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                echo '⚠️  Webhook error while validating signature.';
                http_response_code(400);
                exit();
            }
        }

        //print_r($event->data->object->charges);
        //print_r($event);

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                //$customer = \Stripe\Customer::retrieve($event->data->customer);
                //print_r($event->data->customer);
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                // Then define and call a method to handle the successful payment intent.
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            case 'payment_method.attached':
                $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
                // Then define and call a method to handle the successful attachment of a PaymentMethod.
                // handlePaymentMethodAttached($paymentMethod);
                break;
            default:
                // Unexpected event type
                error_log('Received unknown event type');
        }

        http_response_code(200);
    }


    public function actionIndex(): string
    {
        $context = array();

        return $this->render('payment', $context);
    }



    /**
     * User makes payment.
     * @return false|string
     * @throws ApiErrorException
     */
    public function actionPaymentIntent()
    {
        if(Yii::$app->request->isAjax) {
            if (Yii::$app->request->post()) {
                $PROMO_DISCOUNT = 0;
                $post = Yii::$app->request->post();
                $container = json_decode(Yii::$app->user->identity->userParameters->container);

                $currency = $post['CURRENCY'];
                $packet = Packets::findOne(['id' => $post['PRICE_ID']]);

                if($packet == null) {
                    return json_encode([
                        'success' => false,
                        'error' => Yii::$app->devSet->getTranslate('requiredElements')
                    ]);
                }

                if($post['PROMO_APPLIED'] == 'true') {
                    $premiumCode = PremiumCode::findOne(['name' => $post['PROMO_CODE'], 'packetId' => $packet->id]);

                    if($premiumCode == null) {
                        return json_encode([
                            'success' => false,
                            'error' => Yii::$app->devSet->getTranslate('promoDoesNotExist')
                        ]);
                    }

                    if($premiumCode->active == 'no') {
                        return json_encode([
                            'success' => false,
                            'error' => Yii::$app->devSet->getTranslate('promoCodeIsNotActive')
                        ]);
                    }

                    if($premiumCode->type == 'premium') {
                        // If not new user
                        if(Yii::$app->user->identity->userParameters->cp > 10) {
                            return json_encode([
                                'success' => false,
                                'error' => Yii::$app->devSet->getTranslate('notNewUser')
                            ]);
                        }
                    }

                    if ($premiumCode->used >= $premiumCode->nTime) {
                        return json_encode([
                            'success' => false,
                            'error' => Yii::$app->devSet->getTranslate('promoCodeIsNotValid'),
                            'used' => $premiumCode->used,
                            'nTime' => $premiumCode->nTime
                        ]);
                    }

                    $PROMO_DISCOUNT = $premiumCode->discount;

                    if ($PROMO_DISCOUNT <= $packet->discountPercent) {
                        return json_encode([
                            'success' => false,
                            'error' => Yii::$app->devSet->getTranslate('promoDiscountIsLess'),
                            'PROMO_DISCOUNT' => $PROMO_DISCOUNT,
                            'premium-discount' => $premiumCode->discount,
                        ]);
                    }
                }

                \Stripe\Stripe::setApiKey(Yii::$app->params[(Yii::$app->devSet->isLocal()) ? 'testStripeSecretKey' : 'stripeSecretKey']);

                // Price Calculation
                if($PROMO_DISCOUNT > $packet->discountPercent) {
                    $AMOUNT = $packet[$currency] - ($packet[$currency] * $PROMO_DISCOUNT) / 100;
                } else {
                    $AMOUNT = $packet[$currency] - ($packet[$currency] * $packet->discountPercent) / 100;
                }

                $AMOUNT = intval($AMOUNT * 100);

                try {
                    $customerId = Yii::$app->user->identity->userParameters->stripeCustomerId;

                    if ($customerId == null) {
                        $customer = \Stripe\Customer::create([
                            'name' => Yii::$app->user->identity->username,
                            'email' => Yii::$app->user->identity->email
                        ]);

                        $userParameters = UserParameters::findOne(['userId' => Yii::$app->user->id]);
                        $userParameters->stripeCustomerId = $customer->id;
                        $userParameters->save(false);
                        $customerId = $customer->id;
                    }

                    $paymentIntent = \Stripe\PaymentIntent::create([
                        'customer' => $customerId,
                        'amount' => $AMOUNT,
                        'currency' => $currency,
                        'metadata' => [
                            'for' => 'dillbill',
                            'name' => Yii::$app->user->identity->userProfile->name,
                            'surname' => Yii::$app->user->identity->userProfile->surname,
                            'email' => Yii::$app->user->identity->email,
                            'level' => Yii::$app->user->identity->userParameters->currentLevel,
                            'packetId' => $packet->id,
                            'weekdays' => ($container->weekdays == null) ? '_' : $container->weekdays,
                            'availability' => ($container->availability == null) ? '_' : $container->availability,
                            'promoCode' => ($post['PROMO_CODE'] == null) ? '_' : $post['PROMO_CODE']
                        ]
                    ]);

                    return json_encode([
                        'success' => true,
                        'error' => '',
                        'clientSecret' => $paymentIntent->client_secret
                    ]);

                } catch (Error $e) {
                    http_response_code(500);

                    return json_encode([
                        'success' => false,
                        'error' => $e->getMessage()
                    ]);
                }

            }
        }
    }



    public function actionPromo() {
        if(Yii::$app->request->isAjax) {
            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post();

                $packet = Packets::findOne(['id' => $post['PRICE_ID']]);

                $premiumCode = PremiumCode::findOne(['name' => $post['PROMO_CODE'], 'packetId' => $packet->id]);

                if($premiumCode == null) {
                    return json_encode(['success' => false, 'error' => 'null']);
                } else {
                    if($premiumCode->active == 'no') {
                        return json_encode([
                            'success' => false,
                            'error' => 'not active'
                        ]);
                    }

                    if($premiumCode->type == 'premium') {
                        // If not new user
                        if(Yii::$app->user->identity->userParameters->cp > 10) {
                            return json_encode(['success' => false, 'error' => 'not new user']);
                        }
                    }

                    if(intval($premiumCode->discount) <= $packet->discountPercent) {
                        return json_encode([
                            'success' => false,
                            'error' => Yii::$app->devSet->getTranslate('promoDiscountIsLess'),
                            'premiumCode->discount' => intval($premiumCode->discount),
                            'packet->discountPercent' => $packet->discountPercent
                        ]);
                    }

                    if ($premiumCode->used < $premiumCode->nTime) {
                        return json_encode([
                            'success' => true,
                            'error' => '',
                            'promoDiscount' => intval($premiumCode->discount),
                            'used' => $premiumCode->used,
                            'nTime' => $premiumCode->nTime
                        ]);
                    } else {
                        return json_encode([
                            'success' => false,
                            'error' => 'expired premium code'
                        ]);
                    }
                }

            }
        }
    }

}