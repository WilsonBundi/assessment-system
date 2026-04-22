<?php

namespace app\controllers;

use app\models\StudentSupervisorAssignment;
use app\models\StudentSupervisorAssignmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StudentSupervisorAssignmentController implements the CRUD actions for StudentSupervisorAssignment model.
 */
class StudentSupervisorAssignmentController extends Controller
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
     * Lists all StudentSupervisorAssignment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StudentSupervisorAssignmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudentSupervisorAssignment model.
     * @param int $assignment_id Assignment ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($assignment_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($assignment_id),
        ]);
    }

    /**
     * Creates a new StudentSupervisorAssignment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new StudentSupervisorAssignment();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'assignment_id' => $model->assignment_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StudentSupervisorAssignment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $assignment_id Assignment ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($assignment_id)
    {
        $model = $this->findModel($assignment_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'assignment_id' => $model->assignment_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing StudentSupervisorAssignment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $assignment_id Assignment ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($assignment_id)
    {
        $this->findModel($assignment_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the StudentSupervisorAssignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $assignment_id Assignment ID
     * @return StudentSupervisorAssignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($assignment_id)
    {
        if (($model = StudentSupervisorAssignment::findOne(['assignment_id' => $assignment_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
