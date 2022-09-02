<?php
namespace frontend\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use common\models\User;
use yii\helpers\Url;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with this email address.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     * @throws Exception
     */
    public function sendEmail(): bool
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        if (!Yii::$app->devSet->isLocal()) {
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
                    CURLOPT_POSTFIELDS =>'
                    {
                        "to": "'.$user->email.'",
                        "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['resetPassword'].'",
                        "message_data": {
                            "resetPasswordUrl": "'.Url::to(['user/reset-password'], true).'?token='.$user->password_reset_token.'",
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
            } catch (\Exception $exception) {}
        }

        return true;
    }

}
