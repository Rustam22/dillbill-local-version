<?php

namespace backend\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "teachers".
 *
 * @property int $id
 * @property string|null $email
 * @property string $teacherName
 * @property string|null $teacherZoom
 * @property string $image
 * @property string|null $presentation
 * @property string $landing
 * @property int $orderNumber
 * @property string|null $country
 * @property string|null $experience
 * @property string|null $description_az
 * @property string|null $description_en
 * @property string|null $description_ru
 * @property string|null $description_tr
 * @property string|null $description_pt
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Conversation[] $conversation
 */
class Teachers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers';
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
            [['teacherName', 'image'], 'required'],
            [['landing', 'email'], 'string'],
            [['orderNumber', 'created_at', 'updated_at'], 'integer'],
            [['teacherName', 'country', 'experience'], 'string', 'max' => 255],
            [['teacherZoom', 'image', 'presentation', 'description_az', 'description_en', 'description_ru', 'description_tr'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'teacherName' => 'Teacher Name',
            'teacherZoom' => 'Teacher Zoom',
            'image' => 'Image',
            'presentation' => 'Presentation',
            'landing' => 'Landing',
            'orderNumber' => 'Order Number',
            'country' => 'Country',
            'experience' => 'Experience',
            'description_az' => 'Description Az',
            'description_en' => 'Description En',
            'description_ru' => 'Description Ru',
            'description_tr' => 'Description Tr',
            'description_pt' => 'Description Pt',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    /**
     * Gets query for [[Conversation]].
     *
     * @return ActiveQuery
     */
    public function getConversation()
    {
        return $this->hasMany(Conversation::className(), ['tutorId' => 'id']);
    }

}
