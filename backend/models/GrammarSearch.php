<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Grammar;

/**
 * GrammarSearch represents the model behind the search form about `backend\models\Grammar`.
 */
class GrammarSearch extends Grammar
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'orderNumber'], 'integer'],
            [['description', 'url'], 'safe'],
            [['active', 'level', 'type'], 'string'],
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
        $query = Grammar::find();

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
            'orderNumber' => $this->orderNumber,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'active', $this->active])
            ->andFilterWhere(['like', 'level', $this->level])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
