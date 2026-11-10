<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ExpenseTypeSearch extends ExpenseType
{
    public function rules()
    {
        return [[['id', 'status'], 'integer'], [['name'], 'safe']];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $branchId = User::findOne(Yii::$app->user->id)->branch_id;
        $query = ExpenseType::find()->orderBy(['name' => SORT_ASC]);
        \app\components\BranchAccess::apply($query, 'branch_id', $branchId);
        $provider = new ActiveDataProvider(['query' => $query]);
        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(['id' => $this->id, 'status' => $this->status]);
            $query->andFilterWhere(['like', 'name', $this->name]);
        }
        return $provider;
    }
}

