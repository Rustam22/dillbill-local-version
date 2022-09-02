<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "trialConversation".
 *
 * @property int $id
 * @property string $date
 * @property string $startsAt
 * @property string $endsAt
 * @property int|null $tutorId
 * @property string|null $tutorName
 * @property string|null $tutorEmail
 * @property string|null $tutorImage
 * @property string $level
 * @property string|null $createdAt
 * @property string $visible
 * @property string|null $eventId
 *
 * @property Teachers $tutor
 * @property TrialConversationUsers[] $trialConversationUsers
 */
class TrialConversation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trialConversation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'startsAt', 'level', 'visible'], 'required'],
            [['date', 'createdAt'], 'safe'],
            [['tutorId'], 'integer'],
            [['level', 'visible'], 'string'],
            [['startsAt', 'endsAt', 'tutorName', 'tutorEmail', 'eventId'], 'string', 'max' => 255],
            [['tutorImage'], 'string', 'max' => 500],
            [['tutorId'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::className(), 'targetAttribute' => ['tutorId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'startsAt' => 'Starts At',
            'endsAt' => 'Ends At',
            'tutorId' => 'Tutor ID',
            'tutorName' => 'Tutor Name',
            'tutorEmail' => 'Tutor Email',
            'tutorImage' => 'Tutor Image',
            'level' => 'Level',
            'createdAt' => 'Created At',
            'visible' => 'Visible',
            'eventId' => 'Event ID',
        ];
    }

    /**
     * Gets query for [[Tutor]].
     *
     * @return ActiveQuery
     */
    public function getTutor(): ActiveQuery
    {
        return $this->hasOne(Teachers::className(), ['id' => 'tutorId']);
    }

    /**
     * Gets query for [[TrialConversationUsers]].
     *
     * @return ActiveQuery
     */
    public function getTrialConversationUsers(): ActiveQuery
    {
        return $this->hasMany(TrialConversationUsers::className(), ['trialConversationId' => 'id']);
    }
}
