<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "trialConversationUsers".
 *
 * @property int $id
 * @property int $trialConversationId
 * @property string|null $conversationLevel
 * @property string|null $conversationDate
 * @property string|null $startsAT
 * @property string|null $conversationTopic
 * @property string|null $tutorName
 * @property string|null $tutorImage
 * @property string|null $userName
 * @property string|null $userEmail
 * @property string $action
 * @property int $userId
 * @property string $requestDate
 * @property string $requestTime
 *
 * @property TrialConversation $trialConversation
 * @property User $user
 */
class TrialConversationUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trialConversationUsers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trialConversationId', 'userId', 'requestDate', 'requestTime'], 'required'],
            [['trialConversationId', 'userId'], 'integer'],
            [['action'], 'string'],
            [['requestDate'], 'safe'],
            [['conversationLevel', 'conversationDate', 'startsAT', 'conversationTopic', 'tutorName', 'tutorImage', 'userName', 'userEmail'], 'string', 'max' => 255],
            [['requestTime'], 'string', 'max' => 11],
            [['trialConversationId'], 'exist', 'skipOnError' => true, 'targetClass' => TrialConversation::className(), 'targetAttribute' => ['trialConversationId' => 'id']],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trialConversationId' => 'Trial Conversation ID',
            'conversationLevel' => 'Conversation Level',
            'conversationDate' => 'Conversation Date',
            'startsAT' => 'Starts At',
            'conversationTopic' => 'Conversation Topic',
            'tutorName' => 'Tutor Name',
            'tutorImage' => 'Tutor Image',
            'userName' => 'User Name',
            'userEmail' => 'User Email',
            'action' => 'Action',
            'userId' => 'User ID',
            'requestDate' => 'Request Date',
            'requestTime' => 'Request Time',
        ];
    }

    /**
     * Gets query for [[TrialConversation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrialConversation()
    {
        return $this->hasOne(TrialConversation::className(), ['id' => 'trialConversationId']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
}
