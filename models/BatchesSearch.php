<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use yii\data\ActiveDataProvider;
use app\models\Batches;

/**
 * BatchesSearch represents the model behind the search form of `app\models\Batches`.
 */
class BatchesSearch extends Batches
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'start_at', 'end_at', 'generated_by', 'station_id'], 'integer'],
            [['created_at'], 'safe'],
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
      
       
        $query = Batches::find()->where(['station_id'=>User::findOne(Yii::$app->user->id)->branch->station_id]);

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
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'generated_by' => $this->generated_by,
            'station_id' => $this->station_id,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
