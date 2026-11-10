<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Expenditures;

/**
 * ExpendituresSearch represents the model behind the search form about `app\models\Expenditures`.
 */
class ExpendituresSearch extends Expenditures
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['ename', 'etype', 'edate', 'company', 'phone'], 'safe'],
            [['amount'], 'number'],
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
        
        $query = Expenditures::find()
        ->where(['branch_id'=>$branchId])
        ->orderBy('id DESC');

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
        if ( ! is_null($this->edate) && strpos($this->edate, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->edate);
            $query->andFilterWhere(['between', 'edate', $start_date, $end_date]);
            $this->edate = null;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'DATE(edate)' => $this->edate,
            'amount' => $this->amount,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'ename', $this->ename])
            ->andFilterWhere(['like', 'etype', $this->etype])
            ->andFilterWhere(['like', 'company', $this->company])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}
