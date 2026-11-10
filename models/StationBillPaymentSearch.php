<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class StationBillPaymentSearch extends StationBillPayment
{
    public function rules()
    {
        return [
            [['id', 'station_id', 'created_by'], 'integer'],
            [['reference_no', 'paid_by', 'paid_date', 'notification_status', 'created_at'], 'safe'],
            [['amount'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = StationBillPayment::find()->joinWith(['station', 'createdBy'])->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'station_bill_payments.id' => $this->id,
            'station_bill_payments.station_id' => $this->station_id,
            'station_bill_payments.created_by' => $this->created_by,
            'station_bill_payments.amount' => $this->amount,
            'station_bill_payments.paid_date' => $this->paid_date,
        ]);

        $query->andFilterWhere(['like', 'station_bill_payments.reference_no', $this->reference_no])
            ->andFilterWhere(['like', 'station_bill_payments.paid_by', $this->paid_by])
            ->andFilterWhere(['like', 'station_bill_payments.notification_status', $this->notification_status]);

        return $dataProvider;
    }
}
