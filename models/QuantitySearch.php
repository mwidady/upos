<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\components\BranchAccess;

/**
 * QuantitySearch represents the model behind the search form about `app\models\Quantity`.
 */
class QuantitySearch extends Quantity
{
    public $from_date;
    public $to_date;
    public $item_name;
    public $added_by;
    public $branch_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'user_id', 'quantity', 'branch_id'], 'integer'],
            [['quantity'], 'integer', 'min' => 0],
            [['from_date', 'to_date'], 'date', 'format' => 'php:Y-m-d', 'skipOnEmpty' => true],
            [['item_name', 'added_by'], 'string', 'max' => 255],
            [['q_date'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'from_date' => 'From date',
            'to_date' => 'To date',
            'item_name' => 'Item name',
            'quantity' => 'Quantity',
            'added_by' => 'Added by',
            'branch_id' => 'Branch',
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
    public function search($params, $branchId)
    {
        $query = Quantity::find()->joinWith(['item.branch', 'user']);
        BranchAccess::apply($query, 'items.branch_id', $branchId);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 25],
            'sort' => [
                'defaultOrder' => ['q_date' => SORT_DESC, 'id' => SORT_DESC],
                'attributes' => [
                    'q_date' => [
                        'asc' => ['quantity.q_date' => SORT_ASC],
                        'desc' => ['quantity.q_date' => SORT_DESC],
                    ],
                    'quantity' => [
                        'asc' => ['quantity.quantity' => SORT_ASC],
                        'desc' => ['quantity.quantity' => SORT_DESC],
                    ],
                    'id' => [
                        'asc' => ['quantity.id' => SORT_ASC],
                        'desc' => ['quantity.id' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);
        $invalidDateRange = $this->from_date && $this->to_date && $this->from_date > $this->to_date;
        if (!$this->validate() || $invalidDateRange) {
            if ($invalidDateRange) {
                $this->addError('to_date', 'The end date must be on or after the start date.');
            }
            $query->andWhere('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'quantity.id' => $this->id,
            'quantity.item_id' => $this->item_id,
            'quantity.user_id' => $this->user_id,
            'quantity.q_date' => $this->q_date,
            'quantity.quantity' => $this->quantity,
            'items.branch_id' => $this->branch_id,
        ]);
        if ($this->from_date) {
            $query->andWhere(['>=', 'quantity.q_date', $this->from_date . ' 00:00:00']);
        }
        if ($this->to_date) {
            $toExclusive = (new \DateTimeImmutable($this->to_date . ' 00:00:00'))
                ->modify('+1 day')
                ->format('Y-m-d H:i:s');
            $query->andWhere(['<', 'quantity.q_date', $toExclusive]);
        }
        $query->andFilterWhere(['like', 'items.name', $this->item_name]);
        if ($this->added_by !== null && trim($this->added_by) !== '') {
            $query->andWhere([
                'or',
                ['like', 'user.username', $this->added_by],
                ['like', 'user.first_name', $this->added_by],
                ['like', 'user.last_name', $this->added_by],
            ]);
        }

        return $dataProvider;
    }
}
