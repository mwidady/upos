<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\User;
use app\models\UserSearch;
use app\models\UserPasswordForm;
use app\models\AuditTrail;
use app\models\Branch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login','error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            $model->password = md5("12345");
            $model->authKey = '1234563';
            $model->save(false);
            AuditTrail::record(
                AuditTrail::ACTION_CREATE,
                'User',
                'id=' . $model->id,
                [],
                $this->userAuditValues($model),
                'Created user ' . $model->username
            );

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldValues = $this->userAuditValues($model);

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            AuditTrail::record(
                AuditTrail::ACTION_UPDATE,
                'User',
                'id=' . $model->id,
                $oldValues,
                $this->userAuditValues($model),
                'Updated user ' . $model->username
            );
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);

        if ($model->id == Yii::$app->user->id && (int) $model->status === User::STATUS_ENABLED) {
            Yii::$app->session->setFlash('error', 'You cannot disable your own active account.');
            return $this->redirect(['index']);
        }

        $oldStatus = (int) $model->status;
        $model->status = ($oldStatus === User::STATUS_ENABLED)
            ? User::STATUS_DISABLED
            : User::STATUS_ENABLED;
        $model->save(false);
        AuditTrail::record(
            AuditTrail::ACTION_UPDATE,
            'User',
            'id=' . $model->id,
            ['status' => $oldStatus],
            ['status' => (int) $model->status],
            (($model->status == User::STATUS_ENABLED) ? 'Enabled user ' : 'Disabled user ') . $model->username
        );

        Yii::$app->session->setFlash('success', 'User status updated successfully.');
        return $this->redirect(['index']);
    }

    public function actionSetpassword($id)
    {
        $user = $this->findModel($id);
        $form = new UserPasswordForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $user->password = md5($form->new_password);
            $user->save(false);
            AuditTrail::record(
                AuditTrail::ACTION_UPDATE,
                'User',
                'id=' . $user->id,
                [],
                [],
                'Reset password for user ' . $user->username
            );
            Yii::$app->session->setFlash('success', 'Password updated successfully for ' . $user->username . '.');
            return $this->redirect(['view', 'id' => $user->id]);
        }

        return $this->render('set_password', [
            'user' => $user,
            'model' => $form,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $oldValues = $this->userAuditValues($model);
        $username = $model->username;
        $model->delete();
        AuditTrail::record(
            AuditTrail::ACTION_DELETE,
            'User',
            'id=' . $id,
            $oldValues,
            [],
            'Deleted user ' . $username
        );

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionChangepassword()
    {
        $model = new User();
        $connection = \Yii::$app->db;
        if ($model->load(Yii::$app->request->post())) {


            $user_id = Yii::$app->user->id;
            $old_passord = md5($model->password);
            $new_passord = md5($model->new_password);
            $repeat_passord = md5($model->repeat_password);
            $check = User::find()->where(['id' => $user_id,'password' => $old_passord])->count();
            if($check > 0){

                $connection->createCommand("UPDATE user SET password = '".$new_passord."' WHERE id='".$user_id."'")->execute();
                AuditTrail::record(
                    AuditTrail::ACTION_UPDATE,
                    'User',
                    'id=' . $user_id,
                    [],
                    [],
                    'Changed own password'
                );
                return $this->redirect('index');
            }else{
                \Yii::$app->getSession()->setFlash('error', 'Invalid Password');
                return $this->redirect('changepassword');
            }



            //$model->authKey = '1234563';
            //$model->save(false);


        } else {
            return $this->render('change_password', [
                'model' => $model,
            ]);
        }
    }

    public function actionBranch(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $station_id = $parents[0];
                $out = Branch::find()->where(['station_id' => $station_id])->all(); 
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }

    private function userAuditValues(User $user)
    {
        return [
            'username' => $user->username,
            'email' => $user->email,
            'branch_id' => $user->branch_id,
            'station_id' => $user->station_id,
            'status' => $user->status,
        ];
    }
}
