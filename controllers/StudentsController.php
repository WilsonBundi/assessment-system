<?php

namespace app\controllers;

use Yii;
use app\models\Students;
use app\models\StudentsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StudentsController implements the CRUD actions for Students model.
 */
class StudentsController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['index', 'view'],
                            'allow' => true,
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                // Allow all authenticated users to view students
                                return !Yii::$app->user->isGuest;
                            },
                        ],
                        [
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                // Only TP Office can create, update, delete students
                                return \app\components\RbacHelper::isTpOffice();
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
     * Lists all Students models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StudentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Students model.
     * @param int|string $student_id Student ID or Student Registration Number
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($student_id = null)
    {
        // Handle both route parameter and query parameter
        $studentIdentifier = $student_id ?? Yii::$app->request->get('student_reg_no') ?? Yii::$app->request->get('student_id');

        if (!$studentIdentifier) {
            throw new \yii\web\BadRequestHttpException('Missing required parameters: student_id');
        }

        // Check if student_id is numeric (ID) or string (registration number)
        if (is_numeric($studentIdentifier)) {
            $model = Students::findOne(['student_id' => $studentIdentifier]);
        } else {
            $model = Students::findOne(['student_reg_no' => $studentIdentifier]);
        }

        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('The requested student does not exist.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Students model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Students();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'student_id' => $model->student_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Students model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int|string $student_id Student ID or Student Registration Number
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($student_id = null)
    {
        // Handle both route parameter and query parameter
        $studentIdentifier = $student_id ?? Yii::$app->request->get('student_reg_no') ?? Yii::$app->request->get('student_id');

        if (!$studentIdentifier) {
            throw new \yii\web\BadRequestHttpException('Missing required parameters: student_id');
        }

        // Check if student_id is numeric (ID) or string (registration number)
        if (is_numeric($studentIdentifier)) {
            $model = Students::findOne(['student_id' => $studentIdentifier]);
        } else {
            $model = Students::findOne(['student_reg_no' => $studentIdentifier]);
        }

        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('The requested student does not exist.');
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'student_id' => $model->student_reg_no]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Students model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $student_id Student ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($student_id)
    {
        $this->findModel($student_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Students model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $student_id Student ID
     * @return Students the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($student_id)
    {
        if (($model = Students::findOne(['student_id' => $student_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
