<?php

namespace app\controllers;

use app\models\SchoolClass;
use app\models\SchoolClassSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SchoolClassController implements the CRUD actions for SchoolClass model.
 */
class SchoolClassController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all SchoolClass models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SchoolClassSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SchoolClass model.
     * @param int $class_id Class ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($class_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($class_id),
        ]);
    }

    /**
     * Creates a new SchoolClass model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SchoolClass();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'class_id' => $model->class_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SchoolClass model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $class_id Class ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($class_id)
    {
        $model = $this->findModel($class_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'class_id' => $model->class_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SchoolClass model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $class_id Class ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($class_id)
    {
        $this->findModel($class_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SchoolClass model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $class_id Class ID
     * @return SchoolClass the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($class_id)
    {
        if (($model = SchoolClass::findOne(['class_id' => $class_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
