<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TeachersSearch represents the model behind the search form of `backend\models\Teachers`.
 */
class TeachersSearch extends Teachers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'orderNumber', 'created_at', 'updated_at'], 'integer'],
            [['teacherName', 'teacherZoom', 'email', 'image', 'presentation', 'landing', 'country', 'experience', 'description_az', 'description_en', 'description_ru', 'description_tr'], 'safe'],
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
        $query = Teachers::find();

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
            'orderNumber' => $this->orderNumber,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'teacherName', $this->teacherName])
            ->andFilterWhere(['like', 'teacherZoom', $this->teacherZoom])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'presentation', $this->presentation])
            ->andFilterWhere(['like', 'landing', $this->landing])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'experience', $this->experience])
            ->andFilterWhere(['like', 'description_az', $this->description_az])
            ->andFilterWhere(['like', 'description_en', $this->description_en])
            ->andFilterWhere(['like', 'description_ru', $this->description_ru])
            ->andFilterWhere(['like', 'description_tr', $this->description_tr]);

        return $dataProvider;
    }
}
