<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Assessment;
use app\models\ContactForm;
use app\models\School;
use app\models\Grade;
use app\models\LearningArea;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage or dashboard.
     * For authenticated users, loads dashboard statistics into session.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['site/dashboard']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // Clear old dashboard stats cache on successful login
            Yii::$app->session->remove('dashboardStats');
            Yii::$app->session->remove('dashboardStats_time');
            Yii::$app->session->setFlash('success', 'Login successful! Redirecting to dashboard...');
            return $this->redirect(['site/dashboard']);
        } elseif ($model->load(Yii::$app->request->post())) {
            // Login failed - add debug info
            $debug = 'Login attempt failed. ';
            $user = $model->getUser();
            if ($user) {
                $debug .= 'User found: ' . $user->username . ' (ID: ' . $user->user_id . ', Role: ' . $user->role_id . '). ';
            } else {
                $debug .= 'User not found. ';
            }
            $debug .= 'Input: ' . $model->payroll_no;
            Yii::$app->session->setFlash('error', $debug);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Dashboard page for authenticated users.
     *
     * @return string
     */
    public function actionDashboard()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // Current user info (re-used for stats and recent assessments)
        $user = Yii::$app->user->identity;

        // Load dashboard statistics into session
        $session = Yii::$app->session;
            
            // Only fetch stats if not cached in session or session expired
            if (!isset($session['dashboardStats']) || !isset($session['dashboardStats_time'])) {
                
                // Check if user is a Supervisor (role_id = 1)
                if ($user->role_id == 1) {
                    // Supervisor-specific stats
                    $stats = [
                        'totalAssessments' => Assessment::find()
                            ->where(['examiner_user_id' => $user->user_id])
                            ->count(),
                        'totalSchools' => Assessment::find()
                            ->select(['DISTINCT school_id'])
                            ->where(['examiner_user_id' => $user->user_id])
                            ->count(),
                        'totalGrades' => Grade::find()->count(),
                        'totalLearningAreas' => Assessment::find()
                            ->select(['DISTINCT learning_area_id'])
                            ->where(['examiner_user_id' => $user->user_id])
                            ->count(),
                    ];
                } else {
                    // System-wide stats for admin/coordinators
                    $stats = [
                        'totalAssessments' => Assessment::find()->count(),
                        'totalSchools' => School::find()->count(),
                        'totalGrades' => Grade::find()->count(),
                        'totalLearningAreas' => LearningArea::find()->count(),
                    ];
                }
                
                $session['dashboardStats'] = $stats;
                $session['dashboardStats_time'] = time();
            }
            
            // Recent assessments for quick access
            if ($user->role_id == 1) {
                $recentAssessments = Assessment::find()
                    ->where(['examiner_user_id' => $user->user_id])
                    ->andWhere(['or', ['archived' => 0], ['archived' => null]])
                    ->orderBy(['assessment_date' => SORT_DESC, 'assessment_id' => SORT_DESC])
                    ->limit(10)
                    ->all();
            } elseif ($user->role_id == 2) { // Zone Coordinator
                $recentAssessments = Assessment::find()
                    ->joinWith('school')
                    ->where(['school.zone_id' => $user->zone_id])
                    ->andWhere(['or', ['assessment.archived' => 0], ['assessment.archived' => null]])
                    ->orderBy(['assessment.assessment_date' => SORT_DESC, 'assessment.assessment_id' => SORT_DESC])
                    ->limit(10)
                    ->all();
            } elseif ($user->role_id == 4) { // Department Chair
                $recentAssessments = Assessment::find()
                    ->where(['school_id' => $user->school_id])
                    ->andWhere(['or', ['archived' => 0], ['archived' => null]])
                    ->orderBy(['assessment_date' => SORT_DESC, 'assessment_id' => SORT_DESC])
                    ->limit(10)
                    ->all();
            } else {
                $recentAssessments = Assessment::find()
                    ->andWhere(['or', ['archived' => 0], ['archived' => null]])
                    ->orderBy(['assessment_date' => SORT_DESC, 'assessment_id' => SORT_DESC])
                    ->limit(10)
                    ->all();
            }

            return $this->render('dashboard', [
                'stats' => $session['dashboardStats'],
                'recentAssessments' => $recentAssessments,
            ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        // Keep login on landing page only
        return $this->redirect(['site/index']);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays login guide for all actors
     *
     * @return string
     */
    public function actionLoginGuide()
    {
        return $this->render('login-guide');
    }
}
