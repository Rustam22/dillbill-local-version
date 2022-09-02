<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ConversationSearch represents the model behind the search form of `backend\models\Conversation`.
 */
class ConversationSearch extends Conversation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['date', 'startsAt', 'endsAt', 'tutorName', 'tutorImage', 'level', 'createdAt', 'visible'], 'safe'],
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
        $query = Conversation::find();
        $query->joinWith(['teacher t']); //set relation alias

        // add conditions that should always apply here

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
            'date' => $this->date,
            'createdAt' => $this->createdAt,
        ]);

        $query->andFilterWhere(['like', 'startsAt', $this->startsAt])
            ->andFilterWhere(['like', 'endsAt', $this->endsAt])
            ->andFilterWhere(['like', 't.teacherName', $this->tutorName])
            ->andFilterWhere(['like', 'tutorImage', $this->tutorImage])
            ->andFilterWhere(['=', 'level', $this->level])
            ->andFilterWhere(['like', 'visible', $this->visible]);

        return $dataProvider;
    }
}
