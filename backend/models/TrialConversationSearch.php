<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\TrialConversation;

/**
 * TrialConversationSearch represents the model behind the search form about `backend\models\TrialConversation`.
 */
class TrialConversationSearch extends TrialConversation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tutorId'], 'integer'],
            [['date', 'startsAt', 'endsAt', 'tutorName', 'tutorEmail', 'tutorImage', 'level', 'createdAt', 'visible', 'eventId'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = TrialConversation::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date' => $this->date,
            'tutorId' => $this->tutorId,
            'createdAt' => $this->createdAt,
        ]);

        $query->andFilterWhere(['like', 'startsAt', $this->startsAt])
            ->andFilterWhere(['like', 'endsAt', $this->endsAt])
            ->andFilterWhere(['like', 'tutorName', $this->tutorName])
            ->andFilterWhere(['like', 'tutorEmail', $this->tutorEmail])
            ->andFilterWhere(['like', 'tutorImage', $this->tutorImage])
            ->andFilterWhere(['like', 'level', $this->level])
            ->andFilterWhere(['like', 'visible', $this->visible])
            ->andFilterWhere(['like', 'eventId', $this->eventId]);

        return $dataProvider;
    }
}
