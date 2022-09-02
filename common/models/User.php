<?php
namespace common\models;

use backend\models\Feedback;
use DateTime;
use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 *
 * @property UserProfile $userProfile
 * @property UserParameters $userParameters
 * @property Feedback[] $feedbacks
 */

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_ADMIN = 7;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED, self::STATUS_ADMIN]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => [self::STATUS_ACTIVE, self::STATUS_ADMIN] ]);
    }

    /**
     * {@inheritdoc}
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail(string $email)
    {
        return static::findOne(['email' => $email, 'status' => [self::STATUS_ACTIVE, self::STATUS_ADMIN] ]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmailAdmin(string $email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ADMIN]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken(string $token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken(string $token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }


    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws Exception
     */
    public function setPassword(string $password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     * @throws Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     * @throws Exception
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates just verification token
     * @throws Exception
     */
    public function generateVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


    /**
     * Generate Promo Code
     * @param string $name
     * @return string
     */
    public function generatePromoCode(string $name): string
    {
        $int1 = rand(0, 25);
        $int2 = rand(0, 25);
        $int3 = rand(0, 25);
        $a_z = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $randomLetter = $a_z[$int1].$a_z[$int2].$a_z[$int3];

        return $randomLetter.(User::find()->max('id') + 1);
    }


    public function getCpBalance() {
        $user = Yii::$app->user->identity->userParameters;

        $currentDate = date_create(date('Y-m-d'));
        $lastPaymentDate = ($user->lpd === null) ? date_create(date('Y-m-d')) : date_create($user->lpd);
        $dateDifference = date_diff($currentDate, $lastPaymentDate)->format("%a");
        $dateDifference = ($lastPaymentDate > $currentDate) ? (-1) * $dateDifference : $dateDifference;
        $dateDifference = ($dateDifference < 0 ) ? 0 : $dateDifference;
        $result = $user->cpBalance - $dateDifference;

        return ($result < 0)  ?  0 : $result;
    }


    public function getCpBalanceByStartDate() {
        $user = Yii::$app->user->identity->userParameters;

        $currentDate = date_create(date($user->startDate));
        $lastPaymentDate = ($user->lpd === null) ? date_create(date('Y-m-d')) : date_create($user->lpd);
        $dateDifference = date_diff($currentDate, $lastPaymentDate)->format("%a");
        $dateDifference = ($lastPaymentDate > $currentDate) ? (-1) * $dateDifference : $dateDifference;
        $dateDifference = ($dateDifference < 0 ) ? 0 : $dateDifference;
        $result = $user->cpBalance - $dateDifference;

        return ($result < 0)  ?  0 : $result;
    }


    public function getCpBalanceByUser($userId): int
    {
        $user = UserParameters::find()->
                                select(['(`cpBalance` - DATEDIFF(NOW(), `lpd`)) AS balance'])->
                                where(['userId' => $userId])->
                                asArray()->
                                one();

        return ((int)$user['balance'] < 0) ?  0 : (int)$user['balance'];
    }


    /**
     * @throws \Exception
     */
    public function getLessonBalance($userId): int
    {
        $lessons = 0;
        $user = User::findOne(['id' => $userId]);
        $cpBalance = $this->getCpBalanceByUser($userId);
        $schedulesArray = array_map('intval', str_split($user->userParameters->currentSchedule));

        $sd = new DateTime(($user->userParameters->startDate == null) ? 'now' : $user->userParameters->startDate);
        $day = new DateTime('now 00:00');
        $cd = new DateTime('now 00:00');
        $interval = $cd->diff($day);
        $day = ($sd > $cd) ? $sd : $day;

        while ($interval->format('%a') < (int)$cpBalance) {
            if (in_array($day->format('w'), $schedulesArray)) {
                $lessons++;
            }
            $day->modify('+1 day');
            $interval = $cd->diff($day);
        }

        return $lessons;
    }

    /**
     * Gets query for [[UserProfile]].
     *
     * @return ActiveQuery
     */
    public function getUserProfile(): ActiveQuery
    {
        return $this->hasOne(UserProfile::className(), ['userId' => 'id']);
    }

    /**
     * Gets query for [[UserParameters]].
     *
     * @return ActiveQuery
     */
    public function getUserParameters(): ActiveQuery
    {
        return $this->hasOne(UserParameters::className(), ['userId' => 'id']);
    }


    /**
     * Gets query for [[Feedbacks]].
     *
     * @return ActiveQuery
     */
    public function getFeedbacks(): ActiveQuery
    {
        return $this->hasMany(Feedback::className(), ['userId' => 'id']);
    }
}
