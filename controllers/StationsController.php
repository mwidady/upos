<?php

namespace app\controllers;

use Yii;
use app\models\Stations;
use app\models\StationsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * StationsController implements the CRUD actions for Stations model.
 */
class StationsController extends Controller
{
    /**
     * {@inheritdoc}
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
        ];
    }

    /**
     * Lists all Stations models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Stations model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Stations model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Stations();

        if ($model->load(Yii::$app->request->post())) {
            if ($this->saveStationLogo($model) && $model->save(false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Stations model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())
            && $this->saveStationLogo($model)
            && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = ((int) $model->status === Stations::STATUS_ENABLED)
            ? Stations::STATUS_DISABLED
            : Stations::STATUS_ENABLED;
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Station status updated successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Stations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    private function saveStationLogo(Stations $model)
    {
        $image = UploadedFile::getInstance($model, 'slogo');
        if ($image === null) {
            return true;
        }

        $extension = strtolower($image->extension);
        if (!in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
            $model->addError('slogo', 'Please upload a PNG, JPG, GIF, or WebP image.');
            return false;
        }

        $uploadDirectory = Yii::getAlias('@webroot/uploads/stations');
        try {
            if (!FileHelper::createDirectory($uploadDirectory, 0775, true) || !is_writable($uploadDirectory)) {
                throw new \RuntimeException('Upload directory is not writable.');
            }
        } catch (\Throwable $exception) {
            Yii::error('Unable to prepare station logo directory: ' . $exception->getMessage(), __METHOD__);
            $model->addError('slogo', 'The station logo could not be saved. Please contact support.');
            return false;
        }

        $filename = Yii::$app->security->generateRandomString() . '.' . $extension;
        $path = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;
        if (!$image->saveAs($path)) {
            Yii::error('Unable to save uploaded station logo to ' . $path, __METHOD__);
            $model->addError('slogo', 'The station logo could not be saved. Please try again.');
            return false;
        }

        $model->slogo = $filename;
        return true;
    }

    /**
     * Finds the Stations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stations::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
