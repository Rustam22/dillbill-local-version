<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\TrialConversationUsers;

/**
 * TrialConversationUsersSearch represents the model behind the search form of `backend\models\TrialConversationUsers`.
 */
class TrialConversationUsersSearch extends TrialConversationUsers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'trialConversationId', 'userId'], 'integer'],
            [['conversationLevel', 'conversationDate', 'startsAT', 'conversationTopic', 'tutorName', 'tutorImage', 'userName', 'userEmail', 'action', 'requestDate', 'requestTime'], 'safe'],
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
        $query = TrialConversationUsers::find();

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
            'trialConversationId' => $this->trialConversationId,
            'userId' => $this->userId,
            'requestDate' => $this->requestDate,
        ]);

        $query->andFilterWhere(['like', 'conversationLevel', $this->conversationLevel])
            ->andFilterWhere(['like', 'conversationDate', $this->conversationDate])
            ->andFilterWhere(['like', 'startsAT', $this->startsAT])
            ->andFilterWhere(['like', 'conversationTopic', $this->conversationTopic])
            ->andFilterWhere(['like', 'tutorName', $this->tutorName])
            ->andFilterWhere(['like', 'tutorImage', $this->tutorImage])
            ->andFilterWhere(['like', 'userName', $this->userName])
            ->andFilterWhere(['like', 'userEmail', $this->userEmail])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'requestTime', $this->requestTime]);

        return $dataProvider;
    }
}
