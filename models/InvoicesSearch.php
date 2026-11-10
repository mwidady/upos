<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Invoices;
use yii\data\ActiveDataProvider;

/**
 * InvoicesSearch represents the model behind the search form of `app\models\Invoices`.
 */
class InvoicesSearch extends Invoices
{
    public $customer_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'generated_by', 'customer_id'], 'integer'],
            [['invoiceno', 'status', 'paid_time', 'paid_through', 'payment_type', 'created_at', 'customer_name'], 'safe'],
            [['amount'], 'number'],
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
        $user = User::findOne(Yii::$app->user->id);
        $query = Invoices::find()
        ->joinWith(['customer', 'generatedBy'])
        ->orderBy('id DESC');
        \app\components\BranchAccess::apply($query, 'invoices.branch_id', $user->branch_id);

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
            'invoices.id' => $this->id,
            'invoices.amount' => $this->amount,
            'invoices.generated_by' => $this->generated_by,
            'invoices.paid_time' => $this->paid_time,
            'invoices.status' => $this->status,
            'DATE(invoices.created_at)' => $this->created_at,
            'invoices.customer_id' => $this->customer_id,
        ]);

        $query->andFilterWhere(['like', 'invoices.invoiceno', $this->invoiceno])
            ->andFilterWhere(['like', 'invoices.paid_through', $this->paid_through])
            ->andFilterWhere(['like', 'invoices.payment_type', $this->payment_type])
            ->andFilterWhere(['or',
                ['like', 'customers.fname', $this->customer_name],
                ['like', 'customers.lname', $this->customer_name],
                ['like', 'customers.company', $this->customer_name],
            ]);

        return $dataProvider;
    }
}
