<?php
namespace frontend\models;

use common\models\UserParameters;
use common\models\UserProfile;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupUserForm extends Model
{
    public $username;
    public $name;
    public $surname;
    public $email;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            // My changes
            [['name', 'surname', 'email'], 'trim'],
            [['email', 'password', 'name', 'surname'], 'required'],
            [['name', 'surname'], 'string', 'max' => 255],
            ['email', 'email'],
            [['name', 'surname'], 'string', 'min' => 2],
            ['password', 'string', 'min' => 4],
            //['email', 'match', 'pattern' => '/(mail.ru)|(bk.ru)|(list.ru)|(inbox.ru)/', 'not' => true, 'message' => Yii::$app->devSet->getTranslate('wrongTypeMail')],
            //['email', 'match', 'pattern' => '/(teymur.ru)/', 'not' => true, 'message' => Yii::$app->devSet->getTranslate('wrongTypeMail')],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::$app->devSet->getTranslate('mailNotUnique')],
            //[['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator2::className(), 'uncheckedMessage' => 'Please confirm that you are not a bot.'],
        ];
    }


    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     * @throws Exception
     */
    public function signup(): bool
    {
        $user = new User();
        $userProfile = new UserProfile();
        $userParameters = new UserParameters();

        $user->username = strip_tags($this->name).' '.strip_tags($this->surname);
        $user->email = $this->email;

        $userParameters->confirmed = 'no';
        $userParameters->currentLevel = 'empty';
        $userProfile->name = strip_tags($this->name);
        $userProfile->surname = strip_tags($this->surname);

        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $userParameters->promoCode = $user->generatePromoCode(strip_tags($this->name));

        try {
            $user->save();
            $userProfile->link('user', $user);
            $userParameters->link('user', $user);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail(User $user): bool
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
