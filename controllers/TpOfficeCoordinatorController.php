<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * TpOfficeCoordinatorController handles TP Office-specific operations
 */
class TpOfficeCoordinatorController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Logged in users
                        'matchCallback' => function ($rule, $action) {
                            // Only allow TP Office (role_id = 3)
                            $user = Yii::$app->user->identity;
                            return $user && $user->role_id == 3;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'profile' => ['GET', 'POST'],
                    'edit' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * Display TP Office profile
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity; // Get current logged-in TP Office user

        // Get basic user info
        $tpOffice = Users::findOne(['user_id' => $user->user_id]);

        // Get system statistics (TP Office has access to all data)
        $totalAssessments = \app\models\Assessment::find()->count();
        $totalUsers = Users::find()->count();
        $totalSchools = \app\models\School::find()->count();
        $totalGrades = \app\models\Grade::find()->count();
        $totalCompetenceAreas = \app\models\CompetenceArea::find()->count();

        // Get supervisor count
        $totalSupervisors = Users::find()->where(['role_id' => 1])->count();
        
        // Get zone coordinator count
        $totalCoordinators = Users::find()->where(['role_id' => 2])->count();

        // Get assessments by status
        $completedAssessments = \app\models\Assessment::find()
            ->where(['archived' => 0])
            ->andWhere(['not', ['overall_level' => null]])
            ->count();

        $inProgressAssessments = \app\models\Assessment::find()
            ->andWhere(['or', ['archived' => 0], ['archived' => null]])
            ->andWhere(['is', 'overall_level', null])
            ->count();

        // Get TP Office user's role name
        $role = $tpOffice ? $tpOffice->role : null;

        return $this->render('tp-office-profile', [
            'tpOffice' => $tpOffice,
            'role' => $role,
            'totalAssessments' => $totalAssessments,
            'totalUsers' => $totalUsers,
            'totalSchools' => $totalSchools,
            'totalGrades' => $totalGrades,
            'totalCompetenceAreas' => $totalCompetenceAreas,
            'totalSupervisors' => $totalSupervisors,
            'totalCoordinators' => $totalCoordinators,
            'completedAssessments' => $completedAssessments,
            'inProgressAssessments' => $inProgressAssessments,
        ]);
    }

    /**
     * Edit TP Office profile
     */
    public function actionEdit()
    {
        $user = Yii::$app->user->identity;
        $tpOffice = Users::findOne(['user_id' => $user->user_id]);

        if ($tpOffice->load(Yii::$app->request->post()) && $tpOffice->save()) {
            Yii::$app->session->setFlash('success', 'Profile updated successfully!');
            return $this->redirect(['profile']);
        }

        return $this->render('edit-tp-office-profile', [
            'model' => $tpOffice,
        ]);
    }

    /**
     * View master data summary
     */
    public function actionMasterData()
    {
        $masterDataStats = [
            'schools' => \app\models\School::find()->count(),
            'zones' => \app\models\Zone::find()->count(),
            'grades' => \app\models\Grade::find()->count(),
            'learningAreas' => \app\models\LearningArea::find()->count(),
            'competenceAreas' => \app\models\CompetenceArea::find()->count(),
            'strands' => \app\models\Strand::find()->count(),
            'substrands' => \app\models\Substrand::find()->count(),
            'users' => Users::find()->count(),
            'roles' => \app\models\Role::find()->count(),
        ];

        return $this->render('master-data', [
            'stats' => $masterDataStats,
        ]);
    }
}
