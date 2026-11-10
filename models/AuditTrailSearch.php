<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AuditTrailSearch extends AuditTrail
{
    public $username;

    public function rules()
    {
        return [[['id', 'user_id', 'branch_id'], 'integer'], [['action', 'model', 'record_id', 'description', 'route', 'ip_address', 'created_at', 'username'], 'safe']];
    }

    public function scenarios() { return Model::scenarios(); }

    public function search($params)
    {
        $identity = User::findOne(Yii::$app->user->id);
        $query = AuditTrail::find()->alias('audit')->joinWith(['user auditUser'])
            ->andWhere(['audit.branch_id' => $identity->branch_id])->orderBy(['audit.id' => SORT_DESC]);
        $provider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 25]]);
        $provider->sort->attributes['username'] = ['asc' => ['auditUser.username' => SORT_ASC], 'desc' => ['auditUser.username' => SORT_DESC]];
        $this->load($params);
        if (!$this->validate()) { return $provider; }
        $query->andFilterWhere([
            'audit.id' => $this->id, 'audit.user_id' => $this->user_id,
            'audit.action' => $this->action, 'audit.model' => $this->model,
        ]);
        $query->andFilterWhere(['like', 'audit.record_id', $this->record_id]);
        $query->andFilterWhere(['like', 'audit.description', $this->description]);
        $query->andFilterWhere(['like', 'audit.route', $this->route]);
        $query->andFilterWhere(['like', 'audit.ip_address', $this->ip_address]);
        $query->andFilterWhere(['like', 'auditUser.username', $this->username]);
        if ($this->created_at) { $query->andWhere(['DATE(audit.created_at)' => date('Y-m-d', strtotime($this->created_at))]); }
        return $provider;
    }
}
