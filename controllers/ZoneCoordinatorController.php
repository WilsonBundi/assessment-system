<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Assessment;
use app\models\AssessmentSearch;
use app\models\School;
use app\models\StudentSupervisorAssignment;
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
                    'get-zone-schools' => ['GET'],
                    'get-school-students' => ['GET'],
                    'get-profile-data' => ['GET'],
                    'manage-zones' => ['GET'],
                    'get-school-students-with-status' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Display zone coordinator profile with assigned zones, schools, and students
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity; // Get current logged-in zone coordinator

        // Get basic user info
        $coordinator = Users::findOne(['user_id' => $user->user_id]);

        // Get zones assigned to this coordinator via the assignment table
        $assignedZones = \app\models\Zone::find()
            ->innerJoin('user_zones', 'user_zones.zone_id = zone.zone_id AND user_zones.user_id = :userId', [':userId' => $user->user_id])
            ->all();

        $assignedZoneIds = array_map(function($zone) {
            return $zone->zone_id;
        }, $assignedZones);

        // Get all schools and students in assigned zones
        $zoneData = [];
        foreach ($assignedZones as $zone) {
            $schools = \app\models\School::find()
                ->where(['zone_id' => $zone->zone_id])
                ->all();

            $schoolData = [];
            foreach ($schools as $school) {
                $students = \app\models\Students::find()
                    ->where(['school_id' => $school->school_id])
                    ->all();

                $schoolData[] = [
                    'school' => $school,
                    'students' => $students,
                    'studentCount' => count($students)
                ];
            }

            $zoneData[] = [
                'zone' => $zone,
                'schools' => $schoolData,
                'schoolCount' => count($schools),
                'totalStudents' => array_sum(array_column($schoolData, 'studentCount'))
            ];
        }

        // Get all supervisors with contact details
        $supervisors = \app\models\Users::find()
            ->where(['role_id' => 3]) // Supervisors
            ->orderBy(['name' => SORT_ASC])
            ->all();

        // Get assessment statistics for reviewed assessments only in assigned zones
        $zoneCondition = ['school.zone_id' => $assignedZoneIds ?: [0]];

        $totalAssessments = \app\models\Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->andWhere(['or', ['archived' => 0], ['archived' => null]])
            ->count();

        $pendingValidation = \app\models\Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->andWhere(['archived' => 1])
            ->andWhere(['is', 'validated_by', null]) // Submitted but not validated
            ->count();

        $validatedAssessments = \app\models\Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->andWhere(['is not', 'validated_by', null])
            ->count();

        // Get unique schools being assessed within assigned zones
        $schoolCount = \app\models\Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->select(['school_id' => 'assessment.school_id'])
            ->distinct()
            ->count('DISTINCT school_id');

        // Get unique students assessed within assigned zones
        $uniqueStudents = \app\models\Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->select(['student_reg_no' => 'assessment.student_reg_no'])
            ->distinct()
            ->count('DISTINCT student_reg_no');

        // Get assessments for workflow display
        // All submitted assessments for zone coordinator review in assigned zones
        $submittedAssessments = \app\models\Assessment::find()
            ->with(['student', 'examinerUser', 'school']) // Eager load relationships
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->andWhere(['archived' => 1]) // Submitted
            ->andWhere(['is', 'validated_by', null]) // Not yet validated
            ->orderBy(['assessment_date' => SORT_DESC])
            ->limit(20)
            ->all();

        // Recently validated assessments within assigned zones
        $recentlyValidated = \app\models\Assessment::find()
            ->with(['student', 'examinerUser', 'school']) // Eager load relationships
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->andWhere(['is not', 'validated_by', null]) // Has been validated
            ->orderBy(['validated_at' => SORT_DESC])
            ->limit(10)
            ->all();

        // Setup search model for all assessments (coordinator reviews only assigned-zone assessments)
        $searchModel = new AssessmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Filter results to assigned zones only
        $dataProvider->query
            ->andWhere($zoneCondition)
            ->orderBy(['assessment_date' => SORT_DESC])
            ->andWhere(['or', ['archived' => 0], ['archived' => null]])
            ->limit(20);

        $recentAssessments = $dataProvider->getModels();

        // Get coordinator's role name
        $role = $coordinator ? $coordinator->role : null;

        return $this->render('zone-coordinator-profile', [
            'coordinator' => $coordinator,
            'role' => $role,
            'zoneData' => $zoneData,
            'supervisors' => $supervisors,
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

        if ($model->isCompleted) {
            if ($this->request->isPost) {
                Yii::$app->session->setFlash('warning', 'Assessment completed. Editing is not allowed.');
            }

            return $this->render('edit-assessment', [
                'model' => $model,
            ]);
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

        // Get assigned zones for the logged-in coordinator
        $user = Yii::$app->user->identity;
        $assignedZones = \app\models\Zone::find()
            ->innerJoin('user_zones', 'user_zones.zone_id = zone.zone_id AND user_zones.user_id = :userId', [':userId' => $user->user_id])
            ->all();
        $assignedZoneIds = array_map(function($zone) {
            return $zone->zone_id;
        }, $assignedZones);

        $zoneCondition = ['school.zone_id' => $assignedZoneIds ?: [0]];

        // Get current statistics
        $totalAssessments = Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->andWhere(['or', ['archived' => 0], ['archived' => null]])
            ->count();

        $pendingValidation = Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->andWhere(['archived' => 1])
            ->andWhere(['is', 'validated_by', null])
            ->count();

        $validatedAssessments = Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->andWhere(['is not', 'validated_by', null])
            ->count();

        $schoolCount = Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
            ->select(['school_id' => 'assessment.school_id'])
            ->distinct()
            ->count('DISTINCT school_id');

        // Check for updates
        $newValidationsCount = Assessment::find()
            ->innerJoin('school', 'school.school_id = assessment.school_id')
            ->andWhere($zoneCondition)
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

    /**
     * Get schools for a specific zone (AJAX with pagination)
     */
    public function actionGetZoneSchools()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 2) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $zoneId = Yii::$app->request->get('zone_id');
        $page = (int) Yii::$app->request->get('page', 1);
        $perPage = 12; // Show 12 schools per page

        if (!$zoneId) {
            return ['error' => 'Zone ID is required'];
        }

        // Verify zone is assigned to this coordinator
        $user = Yii::$app->user->identity;
        $isAssigned = \app\models\Zone::find()
            ->innerJoin('user_zones', 'user_zones.zone_id = zone.zone_id AND user_zones.user_id = :userId', [':userId' => $user->user_id])
            ->andWhere(['zone.zone_id' => $zoneId])
            ->exists();

        if (!$isAssigned) {
            return ['error' => 'Access denied to this zone'];
        }

        $offset = ($page - 1) * $perPage;

        // Get schools with pagination
        $schools = \app\models\School::find()
            ->where(['zone_id' => $zoneId])
            ->orderBy(['school_name' => SORT_ASC])
            ->offset($offset)
            ->limit($perPage + 1) // Get one extra to check if there are more
            ->all();

        $hasMore = count($schools) > $perPage;
        if ($hasMore) {
            array_pop($schools); // Remove the extra item
        }

        $schoolData = [];
        foreach ($schools as $school) {
            $studentCount = \app\models\Students::find()
                ->where(['school_id' => $school->school_id])
                ->count();

            $schoolData[] = [
                'school' => [
                    'school_id' => $school->school_id,
                    'school_name' => $school->school_name,
                    'zone_id' => $school->zone_id,
                ],
                'studentCount' => $studentCount
            ];
        }

        return [
            'schools' => $schoolData,
            'hasMore' => $hasMore,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    /**
     * Get students for a specific school (AJAX with pagination)
     */
    public function actionGetSchoolStudents()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 2) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $schoolId = Yii::$app->request->get('school_id');
        $page = (int) Yii::$app->request->get('page', 1);
        $perPage = 20; // Show 20 students per page

        if (!$schoolId) {
            return ['error' => 'School ID is required'];
        }

        // Verify school is in a zone assigned to this coordinator
        $user = Yii::$app->user->identity;
        $isAssigned = \app\models\School::find()
            ->innerJoin('zone', 'zone.zone_id = school.zone_id')
            ->innerJoin('user_zones', 'user_zones.zone_id = zone.zone_id AND user_zones.user_id = :userId', [':userId' => $user->user_id])
            ->andWhere(['school.school_id' => $schoolId])
            ->exists();

        if (!$isAssigned) {
            return ['error' => 'Access denied to this school'];
        }

        $offset = ($page - 1) * $perPage;

        // Get students with pagination
        $students = \app\models\Students::find()
            ->where(['school_id' => $schoolId])
            ->orderBy(['surname' => SORT_ASC, 'other_name' => SORT_ASC])
            ->offset($offset)
            ->limit($perPage + 1) // Get one extra to check if there are more
            ->all();

        $hasMore = count($students) > $perPage;
        if ($hasMore) {
            array_pop($students); // Remove the extra item
        }

        $studentData = [];
        foreach ($students as $student) {
            $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $student->student_reg_no]);
            $supervisorName = 'Unassigned';
            $supervisorEmail = null;
            $supervisorPhone = null;
            if ($assignment && $assignment->supervisor_user_id) {
                $supervisor = Users::findOne(['user_id' => $assignment->supervisor_user_id]);
                if ($supervisor) {
                    $supervisorName = $supervisor->name ?: 'Unknown';
                    if ($supervisor->hasAttribute('email')) {
                        $supervisorEmail = $supervisor->email ?: null;
                    } elseif (filter_var($supervisor->username, FILTER_VALIDATE_EMAIL)) {
                        $supervisorEmail = $supervisor->username;
                    }
                    $supervisorPhone = $supervisor->phone ?: null;
                } else {
                    $supervisorName = 'Unknown';
                }
            }

            $studentData[] = [
                'student_id' => $student->student_id,
                'student_reg_no' => $student->student_reg_no,
                'other_name' => $student->other_name,
                'surname' => $student->surname,
                'phone_no' => $student->phone_no,
                'email' => $student->email,
                'school_id' => $student->school_id,
                'supervisorName' => $supervisorName,
                'supervisorEmail' => $supervisorEmail,
                'supervisorPhone' => $supervisorPhone,
            ];
        }

        return [
            'students' => $studentData,
            'hasMore' => $hasMore,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    /**
     * Manage zones, schools, and students - dedicated page for zone coordinators
     */
    public function actionManageZones()
    {
        $user = Yii::$app->user->identity;

        // Get zones assigned to this coordinator via the assignment table
        $assignedZones = \app\models\Zone::find()
            ->innerJoin('user_zones', 'user_zones.zone_id = zone.zone_id AND user_zones.user_id = :userId', [':userId' => $user->user_id])
            ->all();

        // Get all schools in assigned zones for dropdown
        $schools = [];
        foreach ($assignedZones as $zone) {
            $zoneSchools = \app\models\School::find()
                ->where(['zone_id' => $zone->zone_id])
                ->orderBy(['school_name' => SORT_ASC])
                ->all();
            $schools[$zone->zone_id] = $zoneSchools;
        }

        return $this->render('manage-zones', [
            'assignedZones' => $assignedZones,
            'schools' => $schools,
        ]);
    }

    /**
     * Get students for a specific school with validation status (AJAX)
     */
    public function actionGetSchoolStudentsWithStatus()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 2) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $schoolId = Yii::$app->request->get('school_id');
        $page = (int) Yii::$app->request->get('page', 1);
        $perPage = 20;

        if (!$schoolId) {
            return ['error' => 'School ID is required'];
        }

        // Verify school is in a zone assigned to this coordinator
        $user = Yii::$app->user->identity;
        $isAssigned = \app\models\School::find()
            ->innerJoin('zone', 'zone.zone_id = school.zone_id')
            ->innerJoin('user_zones', 'user_zones.zone_id = zone.zone_id AND user_zones.user_id = :userId', [':userId' => $user->user_id])
            ->andWhere(['school.school_id' => $schoolId])
            ->exists();

        if (!$isAssigned) {
            return ['error' => 'Access denied to this school'];
        }

        $offset = ($page - 1) * $perPage;

        // Get students with assessment validation status
        $students = \app\models\Students::find()
            ->where(['school_id' => $schoolId])
            ->orderBy(['surname' => SORT_ASC, 'other_name' => SORT_ASC])
            ->offset($offset)
            ->limit($perPage + 1)
            ->all();

        $hasMore = count($students) > $perPage;
        if ($hasMore) {
            array_pop($students);
        }

        $studentData = [];
        foreach ($students as $student) {
            // Check if student has any assessments and their validation status
            $assessments = \app\models\Assessment::find()
                ->where(['student_reg_no' => $student->student_reg_no])
                ->orderBy(['assessment_date' => SORT_DESC, 'assessment_id' => SORT_DESC])
                ->all();

            $hasAssessments = count($assessments) > 0;
            $validatedCount = 0;
            $totalAssessments = count($assessments);

            foreach ($assessments as $assessment) {
                if ($assessment->validated_by) {
                    $validatedCount++;
                }
            }

            $validationStatus = 'Not Validated';
            if ($totalAssessments > 0) {
                if ($validatedCount === $totalAssessments) {
                    $validationStatus = 'Fully Validated';
                } elseif ($validatedCount > 0) {
                    $validationStatus = 'Partially Validated';
                } else {
                    $validationStatus = 'Not Validated';
                }
            }

            $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $student->student_reg_no]);
            $supervisorName = 'Unassigned';
            $supervisorEmail = null;
            $supervisorPhone = null;
            if ($assignment && $assignment->supervisor_user_id) {
                $supervisor = Users::findOne(['user_id' => $assignment->supervisor_user_id]);
                if ($supervisor) {
                    $supervisorName = $supervisor->name ?: 'Unknown';
                    if ($supervisor->hasAttribute('email')) {
                        $supervisorEmail = $supervisor->email ?: null;
                    } elseif (filter_var($supervisor->username, FILTER_VALIDATE_EMAIL)) {
                        $supervisorEmail = $supervisor->username;
                    }
                    $supervisorPhone = $supervisor->phone ?: null;
                } else {
                    $supervisorName = 'Unknown';
                }
            }

            $assessmentId = null;
            $assessmentAction = null;
            if (!empty($assessments)) {
                // Prefer a submitted assessment that still needs validation
                foreach ($assessments as $assessment) {
                    if ($assessment->archived == 1 && !$assessment->validated_by) {
                        $assessmentId = $assessment->assessment_id;
                        $assessmentAction = 'validate';
                        break;
                    }
                }
                if (!$assessmentId) {
                    $latestAssessment = $assessments[0];
                    $assessmentId = $latestAssessment->assessment_id;
                    $assessmentAction = 'review';
                }
            }

            $studentData[] = [
                'student_id' => $student->student_id,
                'student_reg_no' => $student->student_reg_no,
                'other_name' => $student->other_name,
                'surname' => $student->surname,
                'phone_no' => $student->phone_no,
                'email' => $student->email,
                'school_id' => $student->school_id,
                'hasAssessments' => $hasAssessments,
                'totalAssessments' => $totalAssessments,
                'validatedAssessments' => $validatedCount,
                'validationStatus' => $validationStatus,
                'supervisorName' => $supervisorName,
                'supervisorEmail' => $supervisorEmail,
                'supervisorPhone' => $supervisorPhone,
                'assessmentId' => $assessmentId,
                'assessmentAction' => $assessmentAction,
            ];
        }

        return [
            'students' => $studentData,
            'hasMore' => $hasMore,
            'page' => $page,
            'perPage' => $perPage
        ];
    }
}

