<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transactions;

/**
 * TransactionsSerach represents the model behind the search form about `app\models\Transactions`.
 */
class TransactionsSerach extends Transactions
{
    public $fullname;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'quantity', 'user_id'], 'integer'],
            [['item_id', 'customer_id'], 'safe'],
            [['trans_date', 'ts_time', 'payment_type','price', 'transaction_no', 'status','fullname'], 'safe'],
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
        $branchId= User::findOne(Yii::$app->user->id)->branch_id;
        
        
        $query = Transactions::find()->joinWith('item')->joinWith('invoice')
        ->where(['invoices.status' => 'PAID'])
        ->where(['invoices.branch_id'=>$branchId])
        ->orderBy('transactions.id DESC');

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

        if ( ! is_null($this->trans_date) && strpos($this->trans_date, ' - ') !== false ) {
           list($start_date, $end_date) = explode(' - ', $this->trans_date);
           $query->andFilterWhere(['between', 'trans_date', $start_date, $end_date]);
            $this->trans_date = null;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'DATE(trans_date)' => $this->trans_date,
            'ts_time' => $this->ts_time
        ]);

        $query->andFilterWhere(['like', 'payment_type', $this->payment_type])
            ->andFilterWhere(['like', 'transaction_no', $this->transaction_no])
            ->andFilterWhere(['like', 'customers.lname', $this->customer_id])
            ->andFilterWhere(['like', 'items.name', $this->item_id])
            ->andFilterWhere(['like', 'price', $this->price]);

        return $dataProvider;
    }
}
