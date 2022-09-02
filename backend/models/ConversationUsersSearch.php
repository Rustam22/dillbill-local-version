<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ConversationUsersSearch represents the model behind the search form of `backend\models\ConversationUsers`.
 */
class ConversationUsersSearch extends ConversationUsers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'conversationId', 'userId'], 'integer'],
            [['requestDate', 'requestTime'], 'safe'],
            [['conversationLevel', 'conversationDate', 'startsAT', 'tutorName', 'tutorImage', 'userName', 'userEmail', 'action'], 'string'],
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
        $query = ConversationUsers::find();

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

        ]);

        $query->andFilterWhere(['like', 'requestTime', $this->requestTime])
            ->andFilterWhere(['like', 'conversationId', $this->conversationId])
            ->andFilterWhere(['like', 'userId', $this->userId])
            ->andFilterWhere(['like', 'requestDate', $this->requestDate])
            ->andFilterWhere(['=', 'conversationLevel', $this->conversationLevel])
            ->andFilterWhere(['like', 'startsAT', $this->startsAT])
            ->andFilterWhere(['like', 'conversationDate', $this->conversationDate])
            ->andFilterWhere(['like', 'tutorName', $this->tutorName])
            ->andFilterWhere(['like', 'tutorImage', $this->tutorImage])
            ->andFilterWhere(['like', 'userName', $this->userName])
            ->andFilterWhere(['like', 'userEmail', $this->userEmail])
            ->andFilterWhere(['like', 'action', $this->action]);

        return $dataProvider;
    }
}
