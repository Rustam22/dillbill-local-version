<?php

namespace backend\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "feedback".
 *
 * @property int $id
 * @property int $userId
 * @property int $classId
 * @property int $tutorId
 * @property string|null $topic
 * @property int $score
 * @property string|null $comment
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Conversation $class
 * @property User $user
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'classId', 'score', 'tutorId'], 'required'],
            [['userId', 'classId', 'score', 'created_at', 'updated_at'], 'integer'],
            [['comment', 'topic'], 'string'],
            [['classId'], 'exist', 'skipOnError' => true, 'targetClass' => Conversation::className(), 'targetAttribute' => ['classId' => 'id']],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
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
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'classId' => 'Class ID',
            'tutorId' => 'Tutor ID',
            'topic' => 'Topic',
            'score' => 'Score',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Class]].
     *
     * @return ActiveQuery
     */
    public function getClass(): ActiveQuery
    {
        return $this->hasOne(Conversation::className(), ['id' => 'classId']);
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
}
