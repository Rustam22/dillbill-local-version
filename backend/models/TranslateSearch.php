<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Translate;

/**
 * TranslateSearch represents the model behind the search form about `backend\models\Translate`.
 */
class TranslateSearch extends Translate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['keyword', 'az', 'en', 'ru', 'tr', 'es', 'pt'], 'safe'],
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
        $query = Translate::find();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'keyword', $this->keyword])
            ->andFilterWhere(['like', 'az', $this->az])
            ->andFilterWhere(['like', 'en', $this->en])
            ->andFilterWhere(['like', 'ru', $this->ru])
            ->andFilterWhere(['like', 'tr', $this->tr])
            ->andFilterWhere(['like', 'pt', $this->pt]);

        return $dataProvider;
    }
}
