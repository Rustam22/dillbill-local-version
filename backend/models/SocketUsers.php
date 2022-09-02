<?php

namespace backend\models;

/**
 * This is the model class for table "socketUsers".
 *
 * @property int $id
 * @property int $resourceId
 * @property int $userId
 * @property string $email
 * @property string $name
 * @property string $level
 */
class SocketUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'socketUsers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resourceId', 'userId', 'email', 'name', 'level'], 'required'],
            [['resourceId', 'userId'], 'integer'],
            [['level'], 'string'],
            [['email', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resourceId' => 'Resource ID',
            'userId' => 'User ID',
            'email' => 'Email',
            'name' => 'Name',
            'level' => 'Level',
        ];
    }
}
