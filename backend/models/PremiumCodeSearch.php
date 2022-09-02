<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PremiumCode;

/**
 * PremiumCodeSearch represents the model behind the search form about `backend\models\PremiumCode`.
 */
class PremiumCodeSearch extends PremiumCode
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'packetId', 'nTime', 'used', 'created_at', 'updated_at'], 'integer'],
            [['name', 'active', 'type'], 'safe'],
            [['discount'], 'number'],
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
        $query = PremiumCode::find();

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
            'packetId' => $this->packetId,
            'discount' => $this->discount,
            'nTime' => $this->nTime,
            'used' => $this->used,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'active', $this->active]);

        return $dataProvider;
    }
}
