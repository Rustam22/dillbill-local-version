<?php

namespace backend\models;


/**
 * This is the model class for table "review".
 *
 * @property int $id
 * @property string|null $beforeLevel
 * @property string|null $afterLevel
 * @property int $stars
 * @property int $orderNumber
 * @property string $language
 * @property string $description
 * @property string $name
 * @property string|null $image
 * @property string|null $position
 */
class Review extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'review';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stars', 'orderNumber'], 'integer'],
            [['name'], 'required'],
            [['beforeLevel', 'afterLevel', 'name', 'position', 'language'], 'string', 'max' => 255],
            [['description', 'image'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'beforeLevel' => 'Before Level',
            'afterLevel' => 'After Level',
            'stars' => 'Stars',
            'orderNumber' => 'Order Number',
            'language' => 'Language',
            'description' => 'Description',
            'name' => 'Name',
            'image' => 'Image',
            'position' => 'Position',
        ];
    }
}
