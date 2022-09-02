<?php

namespace backend\models;



/**
 * This is the model class for table "trendingTopic".
 *
 * @property int $id
 * @property int $categoryId
 * @property string $categoryName
 * @property int $topicId
 * @property string $description
 * @property string $url
 * @property string $image
 * @property string $date
 */
class TrendingTopic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trendingTopic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['categoryId', 'categoryName', 'topicId', 'description', 'url', 'date'], 'required'],
            [['categoryId', 'topicId'], 'integer'],
            [['date'], 'safe'],
            [['categoryName'], 'string', 'max' => 255],
            [['image'], 'string'],
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
            'categoryId' => 'Category ID',
            'categoryName' => 'Category',
            'topicId' => 'Topic ID',
            'description' => 'Topic Description',
            'url' => 'Url',
            'image' => 'Image',
            'date' => 'Date',
        ];
    }
}
