<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\TodaysGrammar;

/**
 * TodaysGrammarSearch represents the model behind the search form about `backend\models\TodaysGrammar`.
 */
class TodaysGrammarSearch extends TodaysGrammar
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'lessonId'], 'integer'],
            [['startDate', 'level', 'lessonName'], 'safe'],
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
        $query = TodaysGrammar::find();

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
            'startDate' => $this->startDate,
            'lessonId' => $this->lessonId,
        ]);

        $query->andFilterWhere(['like', 'level', $this->level])
            ->andFilterWhere(['like', 'lessonName', $this->lessonName]);

        return $dataProvider;
    }
}
