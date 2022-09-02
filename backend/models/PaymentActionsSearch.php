<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PaymentActions;

/**
 * PaymentActionsSearch represents the model behind the search form of `backend\models\PaymentActions`.
 */
class PaymentActionsSearch extends PaymentActions
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'userId', 'packetId', 'planId', 'scheduleId', 'priceId', 'pricePeriod', 'amount', 'currency'], 'integer'],
            [['userName', 'email', 'packetName', 'planName', 'scheduleName', 'priceName', 'promoCode', 'promoType', 'paymentType', 'dateTime', 'code', 'description', 'reference', 'reimbursement', 'paymentDescription', 'timestamp', 'xid', 'rrn', 'approval', 'pan', 'rc'], 'safe'],
            [['priceDiscount', 'priceTotal', 'paidAmount', 'promoDiscount'], 'number'],
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
        $query = PaymentActions::find();

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
            'userId' => $this->userId,
            'packetId' => $this->packetId,
            'planId' => $this->planId,
            'scheduleId' => $this->scheduleId,
            'priceId' => $this->priceId,
            'pricePeriod' => $this->pricePeriod,
            'priceDiscount' => $this->priceDiscount,
            'priceTotal' => $this->priceTotal,
            'paidAmount' => $this->paidAmount,
            'promoDiscount' => $this->promoDiscount,
            'amount' => $this->amount,
            'currency' => $this->currency,
        ]);

        $query->andFilterWhere(['like', 'userName', $this->userName])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'packetName', $this->packetName])
            ->andFilterWhere(['like', 'planName', $this->planName])
            ->andFilterWhere(['like', 'scheduleName', $this->scheduleName])
            ->andFilterWhere(['like', 'priceName', $this->priceName])
            ->andFilterWhere(['like', 'promoCode', $this->promoCode])
            ->andFilterWhere(['like', 'promoType', $this->promoType])
            ->andFilterWhere(['like', 'paymentType', $this->paymentType])
            ->andFilterWhere(['like', 'dateTime', $this->dateTime])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'reference', $this->reference])
            ->andFilterWhere(['like', 'reimbursement', $this->reimbursement])
            ->andFilterWhere(['like', 'paymentDescription', $this->paymentDescription])
            ->andFilterWhere(['like', 'timestamp', $this->timestamp])
            ->andFilterWhere(['like', 'xid', $this->xid])
            ->andFilterWhere(['like', 'rrn', $this->rrn])
            ->andFilterWhere(['like', 'approval', $this->approval])
            ->andFilterWhere(['like', 'pan', $this->pan])
            ->andFilterWhere(['like', 'rc', $this->rc]);

        return $dataProvider;
    }
}
