<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "userParameters".
 *
 * @property int $id
 * @property int $userId
 * @property string $confirmed
 * @property string|null $availability
 * @property string|null $availabilityLCD
 * @property string $proficiency
 * @property string $startDate
 * @property string $currentLevel
 * @property int|null $currentPacketId
 * @property int $currentSchedule
 * @property string|null $promoCode
 * @property int $cp
 * @property int $cpBalance
 * @property string|null $lpd
 * @property string $googleCalendar
 * @property string|null $calendarGmail
 * @property string|null $stripeCustomerId
 * @property string|null $selectedPriceId
 * @property string|null $trialLessonId
 * @property string|null $container
 *
 * @property User $user
 * @property User $user0
 */
class UserParameters extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'userParameters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['userId'], 'required'],
            [['userId', 'currentPacketId', 'currentSchedule', 'cp', 'cpBalance', 'selectedPriceId', 'trialLessonId'], 'integer'],
            [['confirmed', 'proficiency', 'currentLevel', 'googleCalendar'], 'string'],
            [['availabilityLCD', 'startDate', 'lpd'], 'safe'],
            [['availability', 'promoCode', 'calendarGmail'], 'string', 'max' => 255],
            [['userId'], 'unique'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'confirmed' => 'Confirmed',
            'availability' => 'Availability',
            'availabilityLCD' => 'Availability Lcd',
            'proficiency' => 'Proficiency',
            'startDate' => 'Start Date',
            'currentLevel' => 'Current Level',
            'currentPacketId' => 'Current Packet ID',
            'currentSchedule' => 'Current Schedule',
            'promoCode' => 'Promo Code',
            'cp' => 'Cp',
            'cpBalance' => 'Cp Balance',
            'lpd' => 'Lpd',
            'googleCalendar' => 'Google Calendar',
            'calendarGmail' => 'Calendar Gmail',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * Gets query for [[User0]].
     *
     * @return ActiveQuery
     */
    public function getUser0(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
}
