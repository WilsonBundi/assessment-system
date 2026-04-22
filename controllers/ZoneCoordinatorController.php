<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Assessment;
use app\models\AssessmentSearch;
use app\models\School;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * ZoneCoordinatorController handles zone coordinator-specific operations
 */
class ZoneCoordinatorController extends Controller
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
                            // Only allow Zone Coordinators (role_id = 2)
                            $user = Yii::$app->user->identity;
                            return $user && $user->role_id == 2;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'profile' => ['GET', 'POST'],
                    'edit' => ['GET', 'POST'],
                    'validate-all' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Display zone coordinator profile
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity; // Get current logged-in zone coordinator

        // Get basic user info
        $coordinator = Users::findOne(['user_id' => $user->user_id]);

        // Get assessment statistics for reviewed assessments
        $totalAssessments = Assessment::find()
            ->andWhere(['or', ['archived' => 0], ['archived' => null]])
            ->count();

        $pendingValidation = Assessment::find()
            ->andWhere(['archived' => 1])
            ->andWhere(['is', 'validated_by', null]) // Submitted but not validated
            ->count();

        $validatedAssessments = Assessment::find()
            ->andWhere(['is not', 'validated_by', null])
            ->count();

        // Get unique schools being assessed
        $schoolCount = Assessment::find()
            ->select(['school_id'])
            ->distinct()
            ->count('DISTINCT school_id');

        // Get unique students assessed across all supervisors
        $uniqueStudents = Assessment::find()
            ->select(['student_reg_no'])
            ->distinct()
            ->count('DISTINCT student_reg_no');

// Get assessments for workflow display
        $user = Yii::$app->user->identity;

        // All submitted assessments for zone coordinator review
        $submittedAssessments = Assessment::find()
            ->andWhere(['archived' => 1]) // Submitted
            ->andWhere(['is', 'validated_by', null]) // Not yet validated
            ->orderBy(['assessment_date' => SORT_DESC])
            ->limit(20)
            ->all();

        // Recently validated assessments
        $recentlyValidated = Assessment::find()
            ->andWhere(['is not', 'validated_by', null]) // Has been validated
            ->orderBy(['validated_at' => SORT_DESC])
            ->limit(10)
            ->all();

        // Setup search model for all assessments (coordinator reviews all)
        $searchModel = new AssessmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Order by date descending and limit
        $dataProvider->query->orderBy(['assessment_date' => SORT_DESC])
            ->andWhere(['or', ['archived' => 0], ['archived' => null]])
            ->limit(20);

        $recentAssessments = $dataProvider->getModels();

        // Get coordinator's role name
        $role = $coordinator ? $coordinator->role : null;

        return $this->render('zone-coordinator-profile', [
            'coordinator' => $coordinator,
            'role' => $role,
            'totalAssessments' => $totalAssessments,
            'pendingValidation' => $pendingValidation,
            'validatedAssessments' => $validatedAssessments,
            'schoolCount' => $schoolCount,
            'uniqueStudents' => $uniqueStudents,
            'recentAssessments' => $recentAssessments,
            'submittedAssessments' => $submittedAssessments,
            'recentlyValidated' => $recentlyValidated,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Edit zone coordinator profile
     */
    public function actionEdit()
    {
        $user = Yii::$app->user->identity;
        $coordinator = Users::findOne(['user_id' => $user->user_id]);

        if ($coordinator->load(Yii::$app->request->post()) && $coordinator->save()) {
            Yii::$app->session->setFlash('success', 'Profile updated successfully!');
            return $this->redirect(['profile']);
        }

        return $this->render('edit-zone-coordinator-profile', [
            'model' => $coordinator,
        ]);
    }

    /**
     * Review assessment report
     */
    public function actionReviewAssessment($assessment_id = null, $id = null)
    {
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = \app\models\Assessment::findOne($assessmentId);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        return $this->render('review-assessment', [
            'model' => $model,
        ]);
    }

    /**
     * Edit assessment report
     */
    public function actionEditAssessment($assessment_id = null, $id = null)
    {
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = \app\models\Assessment::findOne($assessmentId);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        // Zone coordinators can edit assessment details (but not evidence)
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Assessment updated successfully.');
                return $this->redirect(['review-assessment', 'assessment_id' => $model->assessment_id]);
            }
        }

        return $this->render('edit-assessment', [
            'model' => $model,
        ]);
    }

    /**
     * Validate assessment report
     */
    public function actionValidateAssessment($assessment_id = null, $id = null)
    {
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = \app\models\Assessment::findOne($assessmentId);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        // Check if assessment is already validated
        if ($model->validated_by) {
            Yii::$app->session->setFlash('error', 'This assessment has already been validated.');
            return $this->redirect(['profile']);
        }

        if ($this->request->isPost) {
            // Mark as validated
            $model->validated_by = Yii::$app->user->id;
            $model->validated_at = date('Y-m-d H:i:s');

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Assessment validated successfully.');
                
                // Notify supervisor of validation
                \app\components\NotificationService::notifyAssessmentValidated($model);
                
                return $this->redirect(['profile']);
            } else {
                Yii::$app->session->setFlash('error', 'Error validating assessment.');
            }
        }

        return $this->render('validate-assessment', [
            'model' => $model,
        ]);
    }

    /**
     * Validate all pending assessments at once
     */
    public function actionValidateAll()
    {
        // Get all pending assessments (not yet validated, with overall_level set)
        $pendingAssessments = Assessment::find()
            ->andWhere(['archived' => 1]) // Submitted
            ->andWhere(['is', 'validated_by', null]) // Not yet validated
            ->andWhere(['is not', 'overall_level', null]) // Has overall_level
            ->all();

        if (empty($pendingAssessments)) {
            Yii::$app->session->setFlash('info', 'No pending assessments to validate.');
            return $this->redirect(['profile']);
        }

        $validatedCount = 0;
        $failedCount = 0;

        foreach ($pendingAssessments as $assessment) {
            // Mark as validated
            $assessment->validated_by = Yii::$app->user->id;
            $assessment->validated_at = date('Y-m-d H:i:s');

            if ($assessment->save(false)) {
                $validatedCount++;
                // Notify supervisor of validation
                \app\components\NotificationService::notifyAssessmentValidated($assessment);
            } else {
                $failedCount++;
            }
        }

        if ($failedCount === 0) {
            Yii::$app->session->setFlash('success', "All {$validatedCount} assessment(s) validated successfully.");
        } else {
            Yii::$app->session->setFlash('warning', "Validated {$validatedCount} assessment(s), but {$failedCount} failed.");
        }

        return $this->redirect(['profile']);
    }

    /**
     * Get profile data for real-time updates (AJAX)
     */
    public function actionGetProfileData()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 2) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $lastUpdate = Yii::$app->request->get('last_update', 0);

        // Get current statistics
        $totalAssessments = Assessment::find()
            ->andWhere(['or', ['archived' => 0], ['archived' => null]])
            ->count();

        $pendingValidation = Assessment::find()
            ->andWhere(['archived' => 1])
            ->andWhere(['is', 'validated_by', null])
            ->count();

        $validatedAssessments = Assessment::find()
            ->andWhere(['is not', 'validated_by', null])
            ->count();

        $schoolCount = Assessment::find()
            ->select(['school_id'])
            ->distinct()
            ->count('DISTINCT school_id');

        // Check for updates
        $newValidationsCount = Assessment::find()
            ->andWhere(['is not', 'validated_by', null])
            ->andWhere(['>', 'validated_at', date('Y-m-d H:i:s', $lastUpdate/1000)])
            ->count();

        $updated = $newValidationsCount > 0;

        return [
            'updated' => $updated,
            'totalAssessments' => $totalAssessments,
            'pendingValidation' => $pendingValidation,
            'validatedAssessments' => $validatedAssessments,
            'schoolCount' => $schoolCount,
            'timestamp' => time() * 1000
        ];
    }
}

