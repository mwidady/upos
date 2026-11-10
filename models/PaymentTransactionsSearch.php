<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PaymentTransactions;

/**
 * PaymentTransactionsSearch represents the model behind the search form of `app\models\PaymentTransactions`.
 */
class PaymentTransactionsSearch extends PaymentTransactions
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'received_by', 'created_by'], 'integer'],
            [['amount_paid', 'amount_remain', 'initial_amount'], 'number'],
            [['paid_through', 'account_no', 'paid_by', 'paid_date', 'created_at'], 'safe'],
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
        $branchId= User::findOne(Yii::$app->user->id)->branch_id;
        
        $query = PaymentTransactions::find()->joinWith('invoice')
        ->where(['invoices.branch_id'=>$branchId]);

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

        if ( ! is_null($this->paid_date) && strpos($this->paid_date, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->paid_date);
            $query->andFilterWhere(['between', 'payment_transactions.paid_date', $start_date, $end_date]);
            $this->paid_date = null;
        }
   

        // grid filtering conditions
        $query->andFilterWhere([
            'payment_transactions.id' => $this->id,
            'payment_transactions.amount_paid' => $this->amount_paid,
            'payment_transactions.amount_remain' => $this->amount_remain,
            'payment_transactions.initial_amount' => $this->initial_amount,
            'payment_transactions.received_by' => $this->received_by,
            'DATE(payment_transactions.paid_date)' => $this->paid_date,
            'payment_transactions.created_by' => $this->created_by,
            'payment_transactions.created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'payment_transactions.paid_through', $this->paid_through])
            ->andFilterWhere(['like', 'payment_transactions.account_no', $this->account_no])
            ->andFilterWhere(['like', 'payment_transactions.paid_by', $this->paid_by]);

        return $dataProvider;
    }
}
