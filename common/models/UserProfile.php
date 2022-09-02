<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "userProfile".
 *
 * @property int $id
 * @property int $userId
 * @property string|null $color
 * @property string|null $name
 * @property string|null $surname
 * @property string|null $phone
 * @property string|null $source
 * @property string|null $aim
 * @property string|null $preliminaryLevel
 * @property string|null $timezone
 *
 * @property User $user
 * @property User $user0
 */
class UserProfile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'userProfile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['userId'], 'required'],
            [['userId'], 'integer'],
            [['color', 'name', 'surname', 'timezone', 'phone', 'source', 'aim', 'preliminaryLevel'], 'string', 'max' => 255],
            [['userId'], 'unique'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
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
            'userId' => 'User ID',
            'color' => 'Color',
            'name' => 'Name',
            'surname' => 'Surname',
            'timezone' => 'Timezone',
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
