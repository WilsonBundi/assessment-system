<?php

namespace app\controllers;

use app\components\RbacHelper;
use app\models\School;
use app\models\SchoolSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * SchoolController implements the CRUD actions for School model.
 */
class SchoolController extends Controller
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
                            'actions' => ['index', 'view'],
                            'allow' => true,
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                // Allow TP Office and Zone Coordinators to view schools
                                return RbacHelper::isTpOffice() || RbacHelper::isZoneCoordinator();
                            },
                        ],
                        [
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                // Only TP Office can create, update, delete schools
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
     * Lists all School models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SchoolSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single School model.
     * @param int $school_id School ID
     * @param int $id Alternative parameter name for School ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($school_id = null, $id = null)
    {
        // Handle both parameter names
        $school_id = $school_id ?? $id ?? Yii::$app->request->get('school_id') ?? Yii::$app->request->get('id');

        if (!$school_id) {
            throw new NotFoundHttpException('School ID is required.');
        }

        $model = $this->findModel($school_id);

        // Zone coordinators can only view schools in zones they're assigned to
        if (RbacHelper::isZoneCoordinator()) {
            $user = Yii::$app->user->identity;
            $isAssigned = \app\models\School::find()
                ->innerJoin('zone', 'zone.zone_id = school.zone_id')
                ->innerJoin('users', 'users.zone_id = zone.zone_id AND users.role_id = 2 AND users.user_id = :userId', [':userId' => $user->user_id])
                ->andWhere(['school.school_id' => $school_id])
                ->exists();

            if (!$isAssigned) {
                throw new \yii\web\ForbiddenHttpException('You can only view schools in zones assigned to you.');
            }
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new School model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new School();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'school_id' => $model->school_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing School model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $school_id School ID
     * @param int $id Alternative parameter name for School ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($school_id = null, $id = null)
    {
        // Handle both parameter names
        $school_id = $school_id ?? $id ?? Yii::$app->request->get('school_id') ?? Yii::$app->request->get('id');
        
        if (!$school_id) {
            throw new NotFoundHttpException('School ID is required.');
        }
        
        $model = $this->findModel($school_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'school_id' => $model->school_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing School model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $school_id School ID
     * @param int $id Alternative parameter name for School ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($school_id = null, $id = null)
    {
        // Handle both parameter names
        $school_id = $school_id ?? $id ?? Yii::$app->request->get('school_id') ?? Yii::$app->request->get('id');
        
        if (!$school_id) {
            throw new NotFoundHttpException('School ID is required.');
        }
        
        $this->findModel($school_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the School model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $school_id School ID
     * @return School the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($school_id)
    {
        if (($model = School::findOne(['school_id' => $school_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
