<?php

namespace backend\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "premiumCode".
 *
 * @property int $id
 * @property string $name
 * @property int $packetId
 * @property float $discount
 * @property int $nTime
 * @property int $used
 * @property string $type
 * @property string $active
 * @property int $created_at
 * @property int $updated_at
 */
class PremiumCode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'premiumCode';
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
    public function rules()
    {
        return [
            [['name', 'packetId', 'discount', 'active', 'type'], 'required'],
            [['packetId', 'nTime', 'used', 'created_at', 'updated_at'], 'integer'],
            [['discount'], 'number'],
            [['active'], 'string'],
            [['name'], 'string', 'max' => 255],
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
            'packetId' => 'Packet',
            'discount' => 'Discount',
            'nTime' => 'N Time',
            'used' => 'Used',
            'type' => 'Type',
            'active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
