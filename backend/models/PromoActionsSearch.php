<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PromoActions;

/**
 * PromoActionsSearch represents the model behind the search form about `backend\models\PromoActions`.
 */
class PromoActionsSearch extends PromoActions
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'givenByID', 'takenByID', 'created_at', 'updated_at'], 'integer'],
            [['givenByUser', 'givenByEmail', 'takenByEmail', 'takenByUser', 'givenByPercent', 'takenByPercent', 'condition', 'date', 'description'], 'safe'],
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
        $query = PromoActions::find();

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
            'givenByID' => $this->givenByID,
            'takenByID' => $this->takenByID,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'givenByUser', $this->givenByUser])
            ->andFilterWhere(['like', 'givenByEmail', $this->givenByEmail])
            ->andFilterWhere(['like', 'takenByEmail', $this->takenByEmail])
            ->andFilterWhere(['like', 'takenByUser', $this->takenByUser])
            ->andFilterWhere(['like', 'condition', $this->condition]);

        return $dataProvider;
    }
}
