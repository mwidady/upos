<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Customers;

class CostomersController extends \yii\web\Controller
{
    public function actionIndex()
    {
        echo "This is the test"; exit;
        return $this->render('index');
    }
    public function actionCreate()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //echo "This is the test of dfgfg";
        $customers =new Customers();
        $customers->scenarios = Customers::SCENARIO_CREATE;
        $customers->attributes = \Yii::$app->request->post();
        if($customers->validate()){
           return array('status' => true);
        }else{
            return array('status' => false,'data' => $customers->getErrors());
        }
        //return array('status' => true);
       // exit;

    }
    public function actionList(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $customers = Customers::find()->all();
        if(count($customers) > 0){
            return array('status' => true,'data'=>$customers);
        }
        else{
            return array('status' => false,'data'=>"No records found");
        }

    }


}
