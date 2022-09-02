<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Feedback;

/**
 * FeedbackSearch represents the model behind the search form of `backend\models\Feedback`.
 */
class FeedbackSearch extends Feedback
{
    public $tutor;
    public $classDate;
    public $startsAt;
    public $level;
    public $username;
    public $email;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'userId', 'classId', 'topic', 'score', 'created_at', 'updated_at'], 'integer'],
            [['comment', 'tutor', 'email', 'username', 'level', 'startsAt', 'classDate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Feedback::find();

        $query->joinWith(['user user']); //set relation alias
        $query->joinWith(['class class']); //set relation alias
        $query->joinWith(['class.teacher teacher']); //set relation alias

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'userId' => $this->userId,
            'classId' => $this->classId,
            'topic' => $this->topic,
            'score' => $this->score,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment])
              ->andFilterWhere(['like', 'teacher.teacherName', $this->tutor])
              ->andFilterWhere(['like', 'class.date', $this->classDate])
              ->andFilterWhere(['like', 'class.startsAt', $this->startsAt])
              ->andFilterWhere(['like', 'class.level', $this->level])
              ->andFilterWhere(['like', 'user.username', $this->username])
              ->andFilterWhere(['like', 'user.email', $this->email]);

        return $dataProvider;
    }
}
