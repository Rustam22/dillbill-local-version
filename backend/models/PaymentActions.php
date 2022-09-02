<?php

namespace backend\models;

/**
 * This is the model class for table "paymentActions".
 *
 * @property int $id
 * @property int $userId
 * @property string|null $userName
 * @property string $email
 * @property int|null $packetId
 * @property string|null $packetName
 * @property int|null $planId
 * @property string|null $planName
 * @property int|null $scheduleId
 * @property string|null $scheduleName
 * @property int|null $priceId
 * @property string|null $priceName
 * @property int|null $pricePeriod
 * @property float|null $priceDiscount
 * @property float|null $priceTotal
 * @property string $paidAmount
 * @property string|null $promoCode
 * @property string|null $promoType
 * @property float|null $promoDiscount
 * @property string|null $paymentType
 * @property string $dateTime
 * @property string|null $code
 * @property string|null $description
 * @property string|null $reference
 * @property int|null $amount
 * @property string|null $reimbursement
 * @property int|null $currency
 * @property string|null $paymentDescription
 * @property string|null $timestamp
 * @property string|null $xid
 * @property string|null $rrn
 * @property string|null $approval
 * @property string|null $pan
 * @property string|null $rc
 */
class PaymentActions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paymentActions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'email', 'packetId', 'paidAmount', 'dateTime'], 'required'],
            [['userId', 'packetId', 'planId', 'scheduleId', 'priceId', 'pricePeriod', 'amount', 'currency'], 'integer'],
            [['priceDiscount', 'priceTotal', 'promoDiscount'], 'number'],
            [['userName', 'email', 'packetName', 'paidAmount', 'planName', 'scheduleName', 'priceName', 'promoCode', 'promoType', 'paymentType', 'dateTime', 'code', 'description', 'reference', 'reimbursement', 'paymentDescription', 'timestamp', 'xid', 'rrn', 'approval', 'pan', 'rc'], 'string', 'max' => 255],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'userName' => 'User Name',
            'email' => 'Email',
            'packetId' => 'Packet ID',
            'packetName' => 'Packet Name',
            'planId' => 'Plan ID',
            'planName' => 'Plan Name',
            'scheduleId' => 'Schedule ID',
            'scheduleName' => 'Schedule Name',
            'priceId' => 'Price ID',
            'priceName' => 'Price Name',
            'pricePeriod' => 'Price Period',
            'priceDiscount' => 'Price Discount',
            'priceTotal' => 'Price Total',
            'paidAmount' => 'Paid Amount',
            'promoCode' => 'Promo Code',
            'promoType' => 'Promo Type',
            'promoDiscount' => 'Promo Discount',
            'paymentType' => 'Payment Type',
            'dateTime' => 'Date Time',
            'code' => 'Code',
            'description' => 'Description',
            'reference' => 'Reference',
            'amount' => 'Amount',
            'reimbursement' => 'Reimbursement',
            'currency' => 'Currency',
            'paymentDescription' => 'Payment Description',
            'timestamp' => 'Timestamp',
            'xid' => 'Xid',
            'rrn' => 'Rrn',
            'approval' => 'Approval',
            'pan' => 'Pan',
            'rc' => 'Rc',
        ];
    }
}
