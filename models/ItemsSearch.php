<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Items;

/**
 * ItemsSearch represents the model behind the search form about `app\models\Items`.
 */
class ItemsSearch extends Items
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uid'], 'integer'],
            [['name', 'description', 'vat', 'unit', 'supplier', 'sphone'], 'safe'],
            [['bprice', 'sprice'], 'number'],
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

        $query = Items::find()->where(['branch_id'=>$branchId])
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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'bprice' => $this->bprice,
            'sprice' => $this->sprice,
            'uid' => $this->uid,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'vat', $this->vat])
            ->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'supplier', $this->supplier])
            ->andFilterWhere(['like', 'sphone', $this->sphone]);

        return $dataProvider;
    }
}
