<?php

namespace backend\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "translate".
 *
 * @property int $id
 * @property string $keyword
 * @property string $az
 * @property string $en
 * @property string $ru
 * @property string|null $tr
 * @property string|null $pt
 * @property int $created_at
 * @property int $updated_at
 */
class Translate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'translate';
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
            [['keyword', 'az', 'en', 'ru', 'pt'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['keyword'], 'string', 'max' => 255],
            [['az', 'en', 'ru', 'pt'], 'string', 'max' => 5555],
            [['tr'], 'string', 'max' => 4500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'keyword' => 'Keyword',
            'az' => 'Az',
            'en' => 'En',
            'ru' => 'Ru',
            'tr' => 'Tr',
            'es' => 'Es',
            'pt' => 'Pt',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
