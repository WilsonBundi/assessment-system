<?php

namespace app\controllers;

use app\components\RbacHelper;
use app\models\Grade;
use app\models\GradeSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * GradeController implements the CRUD actions for Grade model.
 */
class GradeController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['view', 'delete'],
                            'roles' => ['@'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['index', 'create', 'update'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return RbacHelper::isTpOffice();
                            },
                        ],
                    ],
                ],
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
     * Lists all Grade models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new GradeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Grade model.
     * @param int $grade_id Grade ID
     * @param int $id Alternative parameter name for Grade ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($grade_id = null, $id = null)
    {
        // Handle both parameter names
        $grade_id = $grade_id ?? $id ?? Yii::$app->request->get('grade_id') ?? Yii::$app->request->get('id');
        
        if (!$grade_id) {
            throw new NotFoundHttpException('Grade ID is required.');
        }
        
        return $this->render('view', [
            'model' => $this->findModel($grade_id),
        ]);
    }

    /**
     * Creates a new Grade model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if (!RbacHelper::isTpOffice()) {
            throw new \yii\web\ForbiddenHttpException('Only TP Office users can create grades.');
        }

        $model = new Grade();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'grade_id' => $model->grade_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Grade model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $grade_id Grade ID
     * @param int $id Alternative parameter name for Grade ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($grade_id = null, $id = null)
    {
        // Handle both parameter names
        $grade_id = $grade_id ?? $id ?? Yii::$app->request->get('grade_id') ?? Yii::$app->request->get('id');
        
        if (!$grade_id) {
            throw new NotFoundHttpException('Grade ID is required.');
        }
        
        if (!RbacHelper::isTpOffice()) {
            throw new \yii\web\ForbiddenHttpException('Only TP Office users can update grades.');
        }

        $model = $this->findModel($grade_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'grade_id' => $model->grade_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Grade model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $grade_id Grade ID
     * @param int $id Alternative parameter name for Grade ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($grade_id = null, $id = null)
    {
        // Handle both parameter names
        $grade_id = $grade_id ?? $id ?? Yii::$app->request->get('grade_id') ?? Yii::$app->request->get('id');
        
        if (!$grade_id) {
            throw new NotFoundHttpException('Grade ID is required.');
        }
        
        $grade = $this->findModel($grade_id);
        $assessment = $grade->assessment;

        if (!RbacHelper::isTpOffice()) {
            if ($assessment && $assessment->isCompleted) {
                Yii::$app->session->setFlash('info', 'Assessment completed. Grade deletion is not allowed.');
                return $this->redirect(['/assessment/view', 'assessment_id' => $assessment->assessment_id]);
            }
            throw new \yii\web\ForbiddenHttpException('Only TP Office users can delete grades.');
        }

        $grade->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Grade model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $grade_id Grade ID
     * @return Grade the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($grade_id)
    {
        if (($model = Grade::findOne(['grade_id' => $grade_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
