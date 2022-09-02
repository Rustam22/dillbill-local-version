<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "grammar".
 *
 * @property int $id
 * @property string|null $description
 * @property string|null $url
 * @property string $level
 * @property string|null $type
 * @property int|null $orderNumber
 * @property string|null $active
 */
class Grammar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grammar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['orderNumber'], 'required'],
            [['orderNumber'], 'integer'],
            [['active', 'level', 'type'], 'string'],
            [['description', 'url'], 'string', 'max' => 555],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'url' => 'Url',
            'level' => 'Level',
            'type' => 'Type',
            'orderNumber' => 'Order Number',
            'active' => 'Active',
        ];
    }
}
