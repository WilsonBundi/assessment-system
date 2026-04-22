<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
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
     * Lists all Users models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
     * @param int $user_id User ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($user_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($user_id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Users();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'user_id' => $model->user_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $user_id User ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($user_id)
    {
        $model = $this->findModel($user_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'user_id' => $model->user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $user_id User ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($user_id)
    {
        $this->findModel($user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Display current user profile/assessment dashboard
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new \yii\web\ForbiddenHttpException('You must be logged in to view your profile.');
        }

        $model = $this->findModel($user->user_id);

        // Get assessment statistics for this user
        $totalAssessments = \app\models\Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->count();

        $pendingAssessments = \app\models\Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->andWhere(['is', 'overall_level', null]) // NULL overall_level = incomplete
            ->count();

        $completedAssessments = $totalAssessments - $pendingAssessments;

        // Get unique schools where user conducted assessments
        $schools = \app\models\Assessment::find()
            ->select(['school_id'])
            ->where(['examiner_user_id' => $user->user_id])
            ->distinct()
            ->column();

        $schoolCount = count($schools);

        // Get unique students assessed
        $uniqueStudents = \app\models\Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->distinct()
            ->select('student_reg_no')
            ->column();

        $uniqueStudents = count($uniqueStudents);

        // Setup search model for assessments
        $searchModel = new \app\models\AssessmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Get the query object from the data provider and add user filter
        $dataProvider->query->andWhere(['examiner_user_id' => $user->user_id])
                            ->andWhere(['archived' => 0]);

        // Order by date descending and limit to recent
        $dataProvider->query->orderBy(['assessment_date' => SORT_DESC])
              ->limit(20); // Show more if searching

        $recentAssessments = $dataProvider->getModels();

        // Get user's role name
        $role = $model->role;

        return $this->render('profile', [
            'model' => $model,
            'role' => $role,
            'totalAssessments' => $totalAssessments,
            'pendingAssessments' => $pendingAssessments,
            'completedAssessments' => $completedAssessments,
            'schoolCount' => $schoolCount,
            'uniqueStudents' => $uniqueStudents,
            'recentAssessments' => $recentAssessments,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $user_id User ID
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($user_id)
    {
        if (($model = Users::findOne(['user_id' => $user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
