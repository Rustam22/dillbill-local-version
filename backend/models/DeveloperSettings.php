<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "developerSettings".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $created_at
 * @property int $updated_at
 * @property string $active
 */
class DeveloperSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'developerSettings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'active', 'value'], 'required'],
            [['description', 'active'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'value' => 'Value',
            'active' => 'Active',
        ];
    }

    /**
     * @param string $parameter
     * @return mixed|null
     */
    public static function getSetting($parameter) {
        $result = static::find()->select(['value', 'active'])->where(['name' => $parameter])->one();
        return ($result->active == 'no') ? null : $result->value;
    }

    /**
     * @param string $parameter
     * @param integer $value
     * @return bool
     */
    public static function setSetting($parameter, $value) {
        $model = static::findOne(['name' => $parameter]);
        $model->value = $value;
        $model->active = 'yes';
        return $model->save();
    }
}
