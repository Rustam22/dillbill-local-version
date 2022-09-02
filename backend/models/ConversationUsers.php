<?php

namespace backend\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "conversationUsers".
 *
 * @property int $id
 * @property int $conversationId
 * @property int $userId
 * @property string $requestDate
 * @property string $requestTime
 *
 * @property string|null $conversationLevel
 * @property string|null $conversationDate
 * @property string|null $startsAT
 * @property string|null $tutorName
 * @property string|null $tutorImage
 * @property string|null $userName
 * @property string|null $userEmail
 * @property string|null $action
 *
 * @property Conversation $conversation
 * @property User $user
 */
class ConversationUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'conversationUsers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['conversationId', 'userId', 'requestDate', 'requestTime'], 'required'],
            [['conversationLevel', 'conversationDate', 'startsAT', 'tutorName', 'tutorImage', 'userName', 'userEmail', 'action'], 'string'],
            [['conversationId', 'userId'], 'integer'],
            [['requestDate'], 'safe'],
            [['requestTime'], 'string', 'max' => 11],
            [['conversationId'], 'exist', 'skipOnError' => true, 'targetClass' => Conversation::className(), 'targetAttribute' => ['conversationId' => 'id']],
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
            'conversationId' => 'Conversation ID',
            'conversationLevel' => 'Conversation Level',
            'conversationDate' => 'Conversation Date',
            'startsAT' => 'Starts AT',
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
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * Gets query for [[Conversation]].
     *
     * @return ActiveQuery
     */
    public function getConversation(): ActiveQuery
    {
        return $this->hasOne(Conversation::className(), ['id' => 'conversationId']);
    }
}
