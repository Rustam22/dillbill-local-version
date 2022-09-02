<?php

namespace backend\models;

/**
 * This is the model class for table "todaysGrammar".
 *
 * @property int $id
 * @property string $startDate
 * @property string $level
 * @property int $lessonId
 * @property string|null $lessonName
 */
class TodaysGrammar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'todaysGrammar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['startDate', 'level', 'lessonId'], 'required'],
            [['startDate'], 'safe'],
            [['level'], 'string'],
            [['lessonId'], 'integer'],
            [['lessonName'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'startDate' => 'Start Date',
            'level' => 'Level',
            'lessonId' => 'Lesson ID',
            'lessonName' => 'Lesson Name',
        ];
    }
}
