<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "packets".
 *
 * @property int $id
 * @property int $period
 * @property int|null $lesson
 * @property string|null $nameKeyword
 * @property string|null $descriptionKeyword
 * @property float $usd
 * @property float $azn
 * @property float $try
 * @property float $brl
 * @property float|null $discountPercent
 */
class Packets extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'packets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['period', 'usd', 'azn', 'try', 'brl'], 'required'],
            [['period', 'lesson'], 'integer'],
            [['usd', 'azn', 'try', 'brl', 'discountPercent'], 'number'],
            [['nameKeyword', 'descriptionKeyword'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'period' => 'Period',
            'lesson' => 'Lesson',
            'nameKeyword' => 'Name Keyword',
            'descriptionKeyword' => 'Description Keyword',
            'usd' => 'Usd',
            'azn' => 'Azn',
            'try' => 'Try',
            'brl' => 'Brl',
            'discountPercent' => 'Discount Percent',
        ];
    }
}
