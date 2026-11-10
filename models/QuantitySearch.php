<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Quantity;

/**
 * QuantitySearch represents the model behind the search form about `app\models\Quantity`.
 */
class QuantitySearch extends Quantity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'user_id', 'quantity'], 'integer'],
            [['q_date'], 'safe'],
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
    public function search($params,$branchId)
    {
        $query = Quantity::find()->joinWith('item'); // use relation for join
        \app\components\BranchAccess::apply($query, 'items.branch_id', $branchId);

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
            'item_id' => $this->item_id,
            'user_id' => $this->user_id,
            'q_date' => $this->q_date,
            'quantity' => $this->quantity,
        ]);

        return $dataProvider;
    }
}
