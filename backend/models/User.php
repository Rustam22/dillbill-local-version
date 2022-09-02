<?php

namespace backend\models;

use common\models\UserParameters;
use common\models\UserProfile;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ConversationUsers[] $conversationUsers
 * @property UserParameters $userParameters
 * @property UserParameters $userParameters0
 * @property User[] $users
 * @property User[] $users0
 * @property UserProfile $userProfile
 * @property UserProfile $userProfile0
 * @property User[] $users1
 * @property User[] $users2
 * @property Feedback[] $feedbacks
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['username',  'email'], 'required'],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'name' => 'Name',
            'surname' => 'Surname',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'verification_token' => 'Verification Token',
            'email' => 'Email',
            'confirmed' => 'Confirmed',
            'availability' => 'Availability',
            'availabilityLCD' => 'Availability Lcd',
            'proficiency' => 'Proficiency',
            'startDate' => 'Start Date',
            'levelUpTestDate' => 'Level Up Test Date',
            'verificationCode' => 'Verification Code',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'mobile' => 'Mobile',
            'birthday' => 'Birthday',
            'currentLevel' => 'Current Level',
            'currentPacketId' => 'Current Packet ID',
            'currentSchedule' => 'Current Schedule',
            'promoCode' => 'Promo Code',
            'condition' => 'Condition',
            'userTimeZone' => 'User Time Zone',
            'lsd' => 'Lsd',
            'lessons' => 'Lessons',
            'cp' => 'Cp',
            'cpBalance' => 'Cp Balance',
            'lpd' => 'Lpd',
            'googleCalendar' => 'Google Calendar',
            'calendarGmail' => 'Calendar Gmail',
            'color' => 'Color',
        ];
    }


    public function getCpBalance() {
        $user = $this->userParameters;

        $currentDate = date_create(date('Y-m-d'));
        $lastPaymentDate = ($user->lpd === null) ? date_create(date('Y-m-d')) : date_create($user->lpd);
        $dateDifference = date_diff($currentDate, $lastPaymentDate)->format("%a");
        $dateDifference = ($lastPaymentDate > $currentDate) ? (-1) * $dateDifference : $dateDifference;
        //$dateDifference = ($dateDifference < 0 ) ? 0 : $dateDifference;
        return $user->cpBalance - $dateDifference;
    }

    /**
     * Gets query for [[ConversationUsers]].
     *
     * @return ActiveQuery
     */
    public function getConversationUsers(): ActiveQuery
    {
        return $this->hasMany(ConversationUsers::className(), ['userId' => 'id']);
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
     * Gets query for [[UserParameters0]].
     *
     * @return ActiveQuery
     */
    public function getUserParameters0(): ActiveQuery
    {
        return $this->hasOne(UserParameters::className(), ['userId' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])->viaTable('userParameters', ['userId' => 'id']);
    }

    /**
     * Gets query for [[Users0]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getUsers0(): ActiveQuery
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])->viaTable('userParameters', ['userId' => 'id']);
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
     * Gets query for [[UserProfile0]].
     *
     * @return ActiveQuery
     */
    public function getUserProfile0(): ActiveQuery
    {
        return $this->hasOne(UserProfile::className(), ['userId' => 'id']);
    }

    /**
     * Gets query for [[Users1]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getUsers1(): ActiveQuery
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])->viaTable('userProfile', ['userId' => 'id']);
    }

    /**
     * Gets query for [[Users2]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getUsers2(): ActiveQuery
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])->viaTable('userProfile', ['userId' => 'id']);
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
