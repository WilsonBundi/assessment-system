<?php

namespace app\controllers;

use app\models\CompetenceArea;
use app\models\CompetenceAreaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CompetenceAreaController implements the CRUD actions for CompetenceArea model.
 */
class CompetenceAreaController extends Controller
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
     * Lists all CompetenceArea models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CompetenceAreaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CompetenceArea model.
     * @param int $competence_id Competence ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($competence_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($competence_id),
        ]);
    }

    /**
     * Creates a new CompetenceArea model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CompetenceArea();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'competence_id' => $model->competence_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CompetenceArea model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $competence_id Competence ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($competence_id)
    {
        $model = $this->findModel($competence_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'competence_id' => $model->competence_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CompetenceArea model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $competence_id Competence ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($competence_id)
    {
        $this->findModel($competence_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CompetenceArea model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $competence_id Competence ID
     * @return CompetenceArea the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($competence_id)
    {
        if (($model = CompetenceArea::findOne(['competence_id' => $competence_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
