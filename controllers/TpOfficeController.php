<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\Assessment;
use app\models\Students;
use app\models\Users;
use app\models\School;
use app\models\Zone;
use app\models\Grade;
use app\models\StudentSupervisorAssignment;
use app\components\NotificationService;

/**
 * TP Office Controller - Handles TP Office user operations
 * View reports, download reports, archive records, manage master data
 */

class TpOfficeController extends Controller
{

    /**
     * Redirects to Substrands management for TP Office
     */
    public function actionSubstrands()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied. TP Office access required.');
        }
        return $this->redirect(['/substrand/index']);
    }
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
                        'roles' => ['@'], // Authenticated users only
                    ],
                ],
            ],
        ];
    }

    /**
     * TP Office Dashboard
     */
    public function actionIndex()
    {
        // Check if user has TP Office role
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) { // Assuming 3 is TP Office role
            throw new \yii\web\ForbiddenHttpException('Access denied. TP Office access required.');
        }

        // Get assessment statistics
        $totalAssessments = Assessment::find()->where(['is not', 'validated_by', null])->count(); // Only validated assessments
        $completedAssessments = Assessment::find()
            ->where(['is not', 'validated_by', null])
            ->andWhere(['not', ['overall_level' => null]])
            ->count();
        $recentAssessments = Assessment::find()
            ->orderBy(['assessment_date' => SORT_DESC])
            ->limit(10)
            ->all();

        $schoolsCount = School::find()->count();
        $zonesCount = Zone::find()->count();
        $gradesCount = Grade::find()->count();

        return $this->render('index', [
            'totalAssessments' => $totalAssessments,
            'completedAssessments' => $completedAssessments,
            'recentAssessments' => $recentAssessments,
            'schoolsCount' => $schoolsCount,
            'zonesCount' => $zonesCount,
            'gradesCount' => $gradesCount,
        ]);
    }

    /**
     * Get dashboard data for real-time updates (AJAX)
     */
    public function actionGetDashboardData()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $lastUpdate = Yii::$app->request->get('last_update', 0);
        $currentTime = time();

        // Get current assessment count
        $totalAssessments = Assessment::find()->where(['is not', 'validated_by', null])->count(); // Only validated assessments
        $completedAssessments = Assessment::find()
            ->where(['is not', 'validated_by', null])
            ->andWhere(['not', ['overall_level' => null]])
            ->count();

        // Check if there are new assessments since last update
        $newAssessmentsCount = Assessment::find()
            ->where(['is not', 'validated_by', null]) // Only check validated assessments
            ->andWhere(['>', 'assessment_date', date('Y-m-d H:i:s', $lastUpdate/1000)])
            ->count();

        $updated = $newAssessmentsCount > 0;

        // Get recent assessments HTML if updated
        $recentAssessmentsHtml = null;
        if ($updated) {
            $recentAssessments = Assessment::find()
                ->where(['is not', 'validated_by', null]) // Only validated assessments
                ->orderBy(['assessment_date' => SORT_DESC])
                ->limit(10)
                ->all();

            $recentAssessmentsHtml = $this->renderPartial('_recent_assessments_table', [
                'recentAssessments' => $recentAssessments
            ]);
        }

        return [
            'updated' => $updated,
            'totalAssessments' => $totalAssessments,
            'completedAssessments' => $completedAssessments,
            'recentAssessmentsHtml' => $recentAssessmentsHtml,
            'timestamp' => $currentTime * 1000
        ];
    }

    /**
     * Get Reports Data for AJAX updates
     */
    public function actionGetReportsData()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $lastUpdate = Yii::$app->request->get('last_update', 0);
        $status = Yii::$app->request->get('status', 'all');
        $currentTime = time();

        // Build query based on status - show submitted assessments for reports
        $query = Assessment::find()
            ->where(['archived' => Assessment::STATUS_SUBMITTED]);

        // Filter by status
        if ($status === 'completed') {
            $query->andWhere(['not', ['overall_level' => null]]);
        } elseif ($status === 'pending') {
            $query->andWhere(['is', 'validated_by', null]);
        }

        // Get current assessment count for this filter
        $totalAssessments = $query->count();

        // Check if there are new assessments since last update for this filter
        $newAssessmentsCount = $query
            ->andWhere(['>', 'assessment_date', date('Y-m-d H:i:s', $lastUpdate/1000)])
            ->count();

        $updated = $newAssessmentsCount > 0;

        // Get reports table HTML if updated
        $reportsTableHtml = null;
        if ($updated) {
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => Assessment::find()
                    ->where(['is not', 'validated_by', null])
                    ->with(['school', 'grades', 'examinerUser'])
                    ->andWhere($status === 'completed' ? ['not', ['overall_level' => null]] :
                              ($status === 'pending' ? ['is', 'overall_level', null] : [])),
                'pagination' => ['pageSize' => 20],
                'sort' => [
                    'defaultOrder' => ['assessment_date' => SORT_DESC],
                ],
            ]);

            $reportsTableHtml = $this->renderPartial('_reports_table', [
                'dataProvider' => $dataProvider
            ]);
        }

        return [
            'updated' => $updated,
            'totalAssessments' => $totalAssessments,
            'reportsTableHtml' => $reportsTableHtml,
            'timestamp' => $currentTime * 1000
        ];
    }

    /**
     * View Assessment Details
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $model = \app\models\Assessment::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Download Reports
     */
    public function actionDownloadReport($id)
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $assessment = Assessment::findOne($id);
        if (!$assessment) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        // Generate PDF report using Dompdf
        $content = $this->renderPartial('_report_pdf', ['assessment' => $assessment]);

        $dompdf = new \Dompdf\Dompdf([ 'isPhpEnabled' => true ]);
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream($assessment->student_reg_no . '_Assessment_Report.pdf', [
            'Attachment' => true
        ]);
    }

    /**
     * Archive Assessment Records
     */
    public function actionArchive($id)
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $assessment = Assessment::findOne($id);
        if (!$assessment) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        // Mark as archived using archived status (0=active, 1=archived)
        $assessment->archived = 1;
        $assessment->archived_at = date('Y-m-d H:i:s');
        $assessment->save(false);

        Yii::$app->session->setFlash('success', 'Assessment record archived successfully.');
        return $this->redirect(['reports']);
    }

    /**
     * Unarchive Assessment Records
     */
    public function actionUnarchive($id)
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $assessment = Assessment::find()->where(['assessment_id' => $id])->one();
        if (!$assessment) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        // Mark as unarchived
        $assessment->archived = 0;
        $assessment->archived_at = null;
        $assessment->save(false);

        Yii::$app->session->setFlash('success', 'Assessment record restored successfully.');
        return $this->redirect(['archived-records']);
    }

    /**
     * View Archived Assessment Records
     */
    public function actionArchivedRecords()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => Assessment::find()
                ->where(['archived' => 1])
                ->with(['school', 'grades', 'examinerUser']),
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['archived_at' => SORT_DESC],
            ],
        ]);

        return $this->render('archived-records', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * View Assessment Reports
     */
    public function actionReports($status = 'all')
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $query = Assessment::find()
            ->where(['archived' => Assessment::STATUS_SUBMITTED])
            ->with(['school', 'grades', 'examinerUser']);

        // Additional filter by status
        if ($status === 'completed') {
            $query->andWhere(['not', ['overall_level' => null]]);
        } elseif ($status === 'pending') {
            $query->andWhere(['is', 'validated_by', null]);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['assessment_date' => SORT_DESC],
            ],
        ]);

        return $this->render('reports', [
            'dataProvider' => $dataProvider,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Display student assignment registry and supervisor list
     */
    public function actionStudentAssignments()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $selectedZoneId = Yii::$app->request->get('zone_id');

        // Get all zones ordered by name
        $zones = Zone::find()->orderBy(['zone_name' => SORT_ASC])->all();

        // Get all supervisors
        $supervisors = Users::find()
            ->where(['role_id' => 1])
            ->with('zone')
            ->orderBy(['zone_id' => SORT_ASC, 'name' => SORT_ASC])
            ->all();

        // Get all students from the official Students table
        $allStudents = Students::find()
            ->joinWith(['school.zone'])
            ->orderBy(['zone.zone_name' => SORT_ASC, 'students.surname' => SORT_ASC, 'students.other_name' => SORT_ASC])
            ->all();

        // Get all unassigned students
        $unassignedStudents = Students::find()
            ->joinWith(['school.zone'])
            ->where(['NOT EXISTS', 
                StudentSupervisorAssignment::find()
                    ->where('student_supervisor_assignment.student_reg_no = students.student_reg_no')
            ])
            ->orderBy(['zone.zone_name' => SORT_ASC, 'students.surname' => SORT_ASC, 'students.other_name' => SORT_ASC])
            ->all();

        // Get all assigned students with their supervisors
        $assignedStudents = Students::find()
            ->joinWith(['school.zone'])
            ->where(['EXISTS', 
                StudentSupervisorAssignment::find()
                    ->where('student_supervisor_assignment.student_reg_no = students.student_reg_no')
                    ->andWhere(['IS NOT', 'student_supervisor_assignment.supervisor_user_id', null])
            ])
            ->orderBy(['zone.zone_name' => SORT_ASC, 'students.surname' => SORT_ASC, 'students.other_name' => SORT_ASC])
            ->all();

        // Organize data by zone
        $dataByZone = [];
        foreach ($zones as $zone) {
            $dataByZone[$zone->zone_id] = [
                'zone' => $zone,
                'supervisors' => [],
                'unassignedStudents' => [],
                'assignedStudents' => [],
            ];
        }

        // Distribute supervisors by zone
        foreach ($supervisors as $supervisor) {
            if (isset($dataByZone[$supervisor->zone_id])) {
                $dataByZone[$supervisor->zone_id]['supervisors'][] = $supervisor;
            }
        }

        // Build supervisors by zone for dropdown
        $supervisorsByZone = [];
        foreach ($supervisors as $supervisor) {
            $zoneId = $supervisor->zone_id;
            $supervisorLabel = $supervisor->name . ' (' . ($supervisor->zone ? $supervisor->zone->zone_name : 'N/A') . ')';
            $supervisorsByZone[$zoneId][$supervisor->user_id] = $supervisorLabel;
        }

        // Distribute unassigned students by zone
        foreach ($unassignedStudents as $student) {
            if ($student->zone && isset($dataByZone[$student->zone->zone_id])) {
                $dataByZone[$student->zone->zone_id]['unassignedStudents'][] = $student;
            }
        }

        // Distribute assigned students by zone
        foreach ($assignedStudents as $student) {
            if ($student->zone && isset($dataByZone[$student->zone->zone_id])) {
                $dataByZone[$student->zone->zone_id]['assignedStudents'][] = $student;
            }
        }

        // Filter by selected zone if specified
        if ($selectedZoneId) {
            $filteredDataByZone = [];
            if (isset($dataByZone[$selectedZoneId])) {
                $filteredDataByZone[$selectedZoneId] = $dataByZone[$selectedZoneId];
            }
            $dataByZone = $filteredDataByZone;
        }

        // Get assignment records for assigned students
        $assignments = [];
        foreach ($assignedStudents as $student) {
            $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $student->student_reg_no]);
            if ($assignment && $assignment->supervisor_user_id) {
                $supervisor = Users::findOne(['user_id' => $assignment->supervisor_user_id]);
                $assignments[$student->student_reg_no] = [
                    'student' => $student,
                    'supervisor' => $supervisor,
                    'assignment' => $assignment
                ];
            }
        }


        return $this->render('student-assignments', [
            'zones' => $zones,
            'dataByZone' => $dataByZone,
            'supervisors' => $supervisors,
            'unassignedStudents' => $unassignedStudents,
            'assignments' => $assignments,
            'supervisorsByZone' => $supervisorsByZone,
        ]);
    }

    /**
     * Display supervisor zone assignments for TP Office
     */
    public function actionSupervisorZones()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $searchQuery = Yii::$app->request->get('search', '');

        $zones = Zone::find()->orderBy(['zone_name' => SORT_ASC])->all();
        $supervisorsQuery = Users::find()
            ->where(['role_id' => 1])
            ->with('zone');

        // Apply search filter if provided
        if (!empty($searchQuery)) {
            $supervisorsQuery->andWhere([
                'or',
                ['ilike', 'name', '%' . $searchQuery . '%'],
                ['ilike', 'username', '%' . $searchQuery . '%'],
                ['ilike', 'email', '%' . $searchQuery . '%'],
                ['ilike', 'payroll_no', '%' . $searchQuery . '%']
            ]);
        }

        $supervisors = $supervisorsQuery->orderBy(['name' => SORT_ASC])->all();

        return $this->render('supervisor-zones', [
            'zones' => $zones,
            'supervisors' => $supervisors,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Update supervisor zone assignment
     */
    public function actionUpdateSupervisorZone()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        if (Yii::$app->request->isPost) {
            $supervisorId = Yii::$app->request->post('supervisor_user_id');
            $zoneId = Yii::$app->request->post('zone_id');

            if (empty($supervisorId) || empty($zoneId)) {
                Yii::$app->session->setFlash('error', 'Supervisor and zone must both be selected.');
                return $this->redirect(['supervisor-zones']);
            }

            $supervisor = Users::findOne(['user_id' => $supervisorId, 'role_id' => 1]);
            if (!$supervisor) {
                Yii::$app->session->setFlash('error', 'Supervisor not found.');
                return $this->redirect(['supervisor-zones']);
            }

            $zone = Zone::findOne(['zone_id' => $zoneId]);
            if (!$zone) {
                Yii::$app->session->setFlash('error', 'Zone not found.');
                return $this->redirect(['supervisor-zones']);
            }

            $supervisor->zone_id = $zoneId;
            if ($supervisor->save()) {
                Yii::$app->session->setFlash('success', 'Supervisor zone updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Unable to update supervisor zone.');
            }
        }

        return $this->redirect(['supervisor-zones']);
    }

    /**
     * Display assigned students
     */
    public function actionAssignedStudents()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        // Get all zones with their assigned students
        $zones = Zone::find()->orderBy(['zone_name' => SORT_ASC])->all();
        $studentsByZone = [];

        foreach ($zones as $zone) {
            $zoneStudents = Students::find()
                ->joinWith(['school'])
                ->where(['school.zone_id' => $zone->zone_id])
                ->orderBy(['students.surname' => SORT_ASC, 'students.other_name' => SORT_ASC])
                ->all();

            $studentsByZone[$zone->zone_id] = [
                'zone' => $zone,
                'students' => []
            ];

            foreach ($zoneStudents as $student) {
                $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $student->student_reg_no]);
                if ($assignment && $assignment->supervisor_user_id !== null) {
                    $studentsByZone[$zone->zone_id]['students'][] = [
                        'user' => $student,
                        'assignment' => $assignment,
                    ];
                }
            }
        }

        return $this->render('assigned-students', [
            'studentsByZone' => $studentsByZone,
        ]);
    }

    /**
     * Display reassign students interface
     */
    public function actionReassignStudents()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $supervisors = Users::find()
            ->where(['role_id' => 1])
            ->with('zone')
            ->orderBy(['name' => SORT_ASC])
            ->all();

        // Get all zones with their assigned students
        $zones = Zone::find()->orderBy(['zone_name' => SORT_ASC])->all();
        $studentsByZone = [];

        foreach ($zones as $zone) {
            $zoneStudents = Students::find()
                ->joinWith(['school'])
                ->where(['school.zone_id' => $zone->zone_id])
                ->orderBy(['students.surname' => SORT_ASC, 'students.other_name' => SORT_ASC])
                ->all();

            $studentsByZone[$zone->zone_id] = [
                'zone' => $zone,
                'students' => []
            ];

            foreach ($zoneStudents as $student) {
                $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $student->student_reg_no]);
                if ($assignment && $assignment->supervisor_user_id !== null) {
                    $studentsByZone[$zone->zone_id]['students'][] = [
                        'user' => $student,
                        'assignment' => $assignment,
                    ];
                }
            }
        }

        return $this->render('reassign-students', [
            'studentsByZone' => $studentsByZone,
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Display overview of assigned and unassigned students
     */
    public function actionOverview()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $supervisors = Users::find()
            ->where(['role_id' => 1])
            ->with('zone')
            ->orderBy(['name' => SORT_ASC])
            ->all();

        // Get all zones with their students organized
        $zones = Zone::find()->orderBy(['zone_name' => SORT_ASC])->all();
        $studentsByZone = [];

        foreach ($zones as $zone) {
            $zoneStudents = Students::find()
                ->joinWith(['school'])
                ->where(['school.zone_id' => $zone->zone_id])
                ->orderBy(['students.surname' => SORT_ASC, 'students.other_name' => SORT_ASC])
                ->all();

            $studentsByZone[$zone->zone_id] = [
                'zone' => $zone,
                'students' => []
            ];

            foreach ($zoneStudents as $student) {
                $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $student->student_reg_no]);
                $studentsByZone[$zone->zone_id]['students'][] = [
                    'user' => $student,
                    'assignment' => $assignment,
                    'assigned' => $assignment && $assignment->supervisor_user_id !== null
                ];
            }
        }

        return $this->render('overview', [
            'studentsByZone' => $studentsByZone,
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Assign supervisor to an unassigned student
     */
    public function actionAssignSupervisor()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        if (Yii::$app->request->isPost) {
            $studentRegNo = trim(Yii::$app->request->post('student_reg_no'));
            $supervisorId = Yii::$app->request->post('supervisor_user_id');

            // Validate input
            if (empty($studentRegNo)) {
                Yii::$app->session->setFlash('error', 'Student registration number is required.');
                return $this->redirect(['student-assignments']);
            }

            if (empty($supervisorId)) {
                Yii::$app->session->setFlash('error', 'Supervisor must be selected.');
                return $this->redirect(['student-assignments']);
            }

            // Check if student exists in the Students table
            $student = Students::findOne(['student_reg_no' => $studentRegNo]);
            if (!$student) {
                Yii::$app->session->setFlash('error', 'Student not found.');
                return $this->redirect(['student-assignments']);
            }

            // Check if student is already assigned to a supervisor
            $existingAssignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $studentRegNo]);
            if ($existingAssignment && $existingAssignment->supervisor_user_id !== null) {
                Yii::$app->session->setFlash('error', 'This student is already assigned to a supervisor. Please use the Reassign option to change.');
                return $this->redirect(['student-assignments', 'tab' => 'assigned']);
            }

            // Check if supervisor exists
            $supervisor = Users::findOne(['user_id' => $supervisorId, 'role_id' => 1]);
            if (!$supervisor) {
                Yii::$app->session->setFlash('error', 'Supervisor not found.');
                return $this->redirect(['student-assignments']);
            }

            // Verify supervisor is in the same zone as student
            $studentZoneId = $student->zone ? $student->zone->zone_id : null;
            if ($studentZoneId !== $supervisor->zone_id) {
                Yii::$app->session->setFlash('error', 'Supervisor must be from the same zone as the student.');
                return $this->redirect(['student-assignments']);
            }

            // Create new assignment or update existing one
            if ($existingAssignment) {
                $assignment = $existingAssignment;
            } else {
                $assignment = new StudentSupervisorAssignment();
                $assignment->student_reg_no = $studentRegNo;
            }

            $assignment->supervisor_user_id = $supervisorId;
            $assignment->zone_id = $studentZoneId;
            if ($assignment->status === null) {
                $assignment->status = 'active';
            }
            if ($assignment->assigned_by === null) {
                $assignment->assigned_by = Yii::$app->user->id;
            }
            $assignment->assigned_at = date('Y-m-d H:i:s');

            if ($assignment->save()) {
                Yii::$app->session->setFlash('success', 'Student assigned to supervisor successfully.');
                NotificationService::notifySupervisorAssigned($supervisor, $student);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to assign student to supervisor. ' . implode(', ', $assignment->getFirstErrors()));
            }
        }

        return $this->redirect(['student-assignments']);
    }

    /**
     * Reassign supervisor to a student
     */
    public function actionReassignSupervisor()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        if (Yii::$app->request->isPost) {
            $studentRegNo = trim(Yii::$app->request->post('student_reg_no'));
            $supervisorId = Yii::$app->request->post('supervisor_user_id');

            if (empty($studentRegNo)) {
                Yii::$app->session->setFlash('error', 'Student registration number is required.');
                return $this->redirect(['student-assignments']);
            }

            $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $studentRegNo]);
            if (!$assignment) {
                Yii::$app->session->setFlash('error', 'Student assignment not found.');
                return $this->redirect(['student-assignments']);
            }

            if (empty($supervisorId)) {
                Yii::$app->session->setFlash('error', 'Supervisor must be selected.');
                return $this->redirect(['student-assignments']);
            }

            $supervisor = Users::findOne(['user_id' => $supervisorId, 'role_id' => 1]);
            if (!$supervisor) {
                Yii::$app->session->setFlash('error', 'A valid supervisor must be selected.');
                return $this->redirect(['student-assignments']);
            }

            $student = Students::findOne(['student_reg_no' => $studentRegNo]);
            if (!$student) {
                Yii::$app->session->setFlash('error', 'Student not found.');
                return $this->redirect(['student-assignments']);
            }

            $studentZoneId = $student->zone ? $student->zone->zone_id : null;
            if ($studentZoneId !== $supervisor->zone_id) {
                Yii::$app->session->setFlash('error', 'Supervisor must be from the same zone as the student.');
                return $this->redirect(['student-assignments', 'tab' => 'assigned']);
            }

            $assignment->supervisor_user_id = $supervisor->user_id;
            $assignment->zone_id = $studentZoneId;
            if ($assignment->status === null) {
                $assignment->status = 'active';
            }
            if ($assignment->assigned_by === null) {
                $assignment->assigned_by = Yii::$app->user->id;
            }
            $assignment->assigned_at = date('Y-m-d H:i:s');
            if ($assignment->save()) {
                Yii::$app->session->setFlash('success', 'Supervisor reassigned successfully.');
                NotificationService::notifySupervisorReassigned($supervisor, $student);
            } else {
                Yii::$app->session->setFlash('error', 'Unable to reassign supervisor.');
            }
        }

        return $this->redirect(['student-assignments']);
    }

    /**
     * Unassign a student (remove supervisor)
     */
    public function actionUnassignStudent()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        if (Yii::$app->request->isPost) {
            $studentRegNo = trim(Yii::$app->request->post('student_reg_no'));

            if (empty($studentRegNo)) {
                Yii::$app->session->setFlash('error', 'Student registration number is required.');
                return $this->redirect(['student-assignments']);
            }

            $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $studentRegNo]);
            if (!$assignment) {
                Yii::$app->session->setFlash('error', 'Student assignment not found.');
                return $this->redirect(['student-assignments']);
            }

            $assignment->supervisor_user_id = null;
            if ($assignment->save()) {
                Yii::$app->session->setFlash('success', 'Student unassigned successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Unable to unassign student.');
            }
        }

        return $this->redirect(['student-assignments']);
    }

    /**
     * Manage Schools
     */
    public function actionSchools()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\School::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('schools', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Zones
     */
    public function actionZones()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\Zone::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('zones', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Grades
     */
    public function actionGrades()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\Grade::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('grades', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Learning Areas
     */
    public function actionLearningAreas()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\LearningArea::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('learning-areas', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Strands
     */
    public function actionStrands()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\Strand::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('strands', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Master Data
     */
    public function actionMasterData()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $stats = [
            'schools' => \app\models\School::find()->count(),
            'zones' => \app\models\Zone::find()->count(),
            'grades' => \app\models\Grade::find()->count(),
            'learningAreas' => \app\models\LearningArea::find()->count(),
            'strands' => \app\models\Strand::find()->count(),
            'substrands' => \app\models\Substrand::find()->count(),
        ];

        return $this->render('master-data', [
            'stats' => $stats,
        ]);
    }

    /**
     * Download assigned students report in Excel format
     */
    public function actionDownloadAssignedStudentsExcel()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        // Get all zones with their assigned students
        $zones = Zone::find()->orderBy(['zone_name' => SORT_ASC])->all();
        $studentsByZone = [];

        foreach ($zones as $zone) {
            $zoneStudents = Users::find()
                ->where(['zone_id' => $zone->zone_id, 'role_id' => 2])
                ->orderBy(['name' => SORT_ASC])
                ->all();

            $studentsByZone[$zone->zone_id] = [
                'zone' => $zone,
                'students' => []
            ];

            foreach ($zoneStudents as $student) {
                $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $student->student_reg_no]);
                if ($assignment && $assignment->supervisor_user_id !== null) {
                    $studentsByZone[$zone->zone_id]['students'][] = [
                        'user' => $student,
                        'assignment' => $assignment,
                    ];
                }
            }
        }

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Assigned Students Report');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set column headers
        $sheet->setCellValue('A3', 'Zone');
        $sheet->setCellValue('B3', 'Student ID');
        $sheet->setCellValue('C3', 'Student Name');
        $sheet->setCellValue('D3', 'Supervisor');
        $sheet->setCellValue('E3', 'Assigned Date');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ];
        $sheet->getStyle('A3:E3')->applyFromArray($headerStyle);

        // Add data
        $row = 4;
        foreach ($studentsByZone as $zoneData) {
            $zone = $zoneData['zone'];
            $assignedZoneStudents = $zoneData['students'];

            if (!empty($assignedZoneStudents)) {
                foreach ($assignedZoneStudents as $studentData) {
                    $student = $studentData['user'];
                    $assignment = $studentData['assignment'];

                    $sheet->setCellValue('A' . $row, $zone->zone_name);
                    $sheet->setCellValue('B' . $row, $student->student_reg_no);
                    $sheet->setCellValue('C' . $row, $student->name);
                    $sheet->setCellValue('D' . $row, $assignment->supervisor->name);
                    $sheet->setCellValue('E' . $row, Yii::$app->formatter->asDate($assignment->assigned_at, 'medium'));

                    $row++;
                }
            }
        }

        // Auto-size columns
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set filename
        $filename = 'assigned_students_report_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Set headers for download
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="' . $filename . '"');
        Yii::$app->response->headers->add('Cache-Control', 'max-age=0');

        // Output file
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        Yii::$app->response->content = $content;
        return Yii::$app->response;
    }

    /**
     * Download assessment reports in Excel format
     */
    public function actionDownloadAssessmentReportsExcel($status = 'all')
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $query = Assessment::find()
            ->where(['is not', 'validated_by', null])
            ->with(['school', 'grades', 'examinerUser']);

        // Additional filter by status
        if ($status === 'completed') {
            $query->andWhere(['not', ['overall_level' => null]]);
        } elseif ($status === 'pending') {
            $query->andWhere(['is', 'overall_level', null]);
        }

        $assessments = $query->orderBy(['assessment_date' => SORT_DESC])->all();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'Assessment Reports');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set column headers
        $sheet->setCellValue('A3', 'Assessment ID');
        $sheet->setCellValue('B3', 'Student ID');
        $sheet->setCellValue('C3', 'School');
        $sheet->setCellValue('D3', 'Assessment Date');
        $sheet->setCellValue('E3', 'Examiner');
        $sheet->setCellValue('F3', 'Overall Level');
        $sheet->setCellValue('G3', 'Status');
        $sheet->setCellValue('H3', 'Validated By');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ];
        $sheet->getStyle('A3:H3')->applyFromArray($headerStyle);

        // Add data
        $row = 4;
        foreach ($assessments as $assessment) {
            $sheet->setCellValue('A' . $row, $assessment->assessment_id);
            $sheet->setCellValue('B' . $row, $assessment->student_reg_no);
            $sheet->setCellValue('C' . $row, $assessment->school->school_name ?? 'N/A');
            $sheet->setCellValue('D' . $row, Yii::$app->formatter->asDate($assessment->assessment_date, 'medium'));
            $sheet->setCellValue('E' . $row, $assessment->examinerUser->name ?? 'N/A');
            $sheet->setCellValue('F' . $row, $assessment->overall_level ?? 'N/A');
            $sheet->setCellValue('G' . $row, $assessment->overall_level ? 'Completed' : 'Pending');
            $sheet->setCellValue('H' . $row, $assessment->validatorUser->name ?? 'N/A');

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set filename
        if ($status === 'completed') {
            $statusLabel = 'completed_';
        } elseif ($status === 'pending') {
            $statusLabel = 'pending_';
        } else {
            $statusLabel = '';
        }
        $filename = 'assessment_reports_' . $statusLabel . date('Y-m-d_H-i-s') . '.xlsx';

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Set headers for download
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="' . $filename . '"');
        Yii::$app->response->headers->add('Cache-Control', 'max-age=0');

        // Output file
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        Yii::$app->response->content = $content;
        return Yii::$app->response;
    }

    /**
     * Get student assignments data for real-time updates (AJAX)
     */
    public function actionGetAssignmentsData()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $lastUpdate = Yii::$app->request->get('last_update', 0);
        $currentTime = time();

        // Get all zones
        $zones = Zone::find()->orderBy(['zone_name' => SORT_ASC])->all();

        // Get all supervisors
        $supervisors = Users::find()
            ->where(['role_id' => 1])
            ->with('zone')
            ->orderBy(['zone_id' => SORT_ASC, 'name' => SORT_ASC])
            ->all();

        // Get all unassigned students
        $unassignedStudents = Students::find()
            ->joinWith(['school.zone'])
            ->where(['NOT EXISTS', 
                StudentSupervisorAssignment::find()
                    ->where('student_supervisor_assignment.student_reg_no = students.student_reg_no')
                    ->andWhere(['IS NOT', 'student_supervisor_assignment.supervisor_user_id', null])
            ])
            ->orderBy(['zone.zone_name' => SORT_ASC, 'students.surname' => SORT_ASC, 'students.other_name' => SORT_ASC])
            ->all();

        // Get all assigned students
        $assignedStudents = Students::find()
            ->joinWith(['school.zone'])
            ->where(['EXISTS', 
                StudentSupervisorAssignment::find()
                    ->where('student_supervisor_assignment.student_reg_no = students.student_reg_no')
                    ->andWhere(['IS NOT', 'student_supervisor_assignment.supervisor_user_id', null])
            ])
            ->orderBy(['zone.zone_name' => SORT_ASC, 'students.surname' => SORT_ASC, 'students.other_name' => SORT_ASC])
            ->all();

        // Check for changes since last update
        $hasChanges = false;
        $latestAssignmentTime = 0;

        // Check assignment timestamps
        $latestAssignment = StudentSupervisorAssignment::find()
            ->orderBy(['assigned_at' => SORT_DESC])
            ->one();

        if ($latestAssignment && $latestAssignment->assigned_at) {
            $latestAssignmentTime = strtotime($latestAssignment->assigned_at) * 1000;
            if ($latestAssignmentTime > $lastUpdate) {
                $hasChanges = true;
            }
        }

        // Check for new supervisors or students (simplified check)
        $supervisorCount = Users::find()->where(['role_id' => 1])->count();
        $studentCount = Students::find()->count();
        $assignmentCount = StudentSupervisorAssignment::find()
            ->where(['IS NOT', 'supervisor_user_id', null])
            ->count();

        // If no previous data, assume changes
        if ($lastUpdate == 0) {
            $hasChanges = true;
        }

        $data = [
            'updated' => $hasChanges,
            'timestamp' => $currentTime * 1000,
            'stats' => [
                'totalSupervisors' => $supervisorCount,
                'totalStudents' => $studentCount,
                'totalAssigned' => $assignmentCount,
                'totalUnassigned' => $studentCount - $assignmentCount,
            ]
        ];

        if ($hasChanges) {
            // Organize data by zone
            $dataByZone = [];
            foreach ($zones as $zone) {
                $dataByZone[$zone->zone_id] = [
                    'zone' => [
                        'zone_id' => $zone->zone_id,
                        'zone_name' => $zone->zone_name,
                    ],
                    'supervisors' => [],
                    'unassignedStudents' => [],
                    'assignedStudents' => [],
                ];
            }

            // Distribute supervisors by zone
            foreach ($supervisors as $supervisor) {
                if (isset($dataByZone[$supervisor->zone_id])) {
                    $dataByZone[$supervisor->zone_id]['supervisors'][] = [
                        'user_id' => $supervisor->user_id,
                        'name' => $supervisor->name,
                        'zone_name' => $supervisor->zone ? $supervisor->zone->zone_name : 'N/A',
                    ];
                }
            }

            // Distribute unassigned students by zone
            foreach ($unassignedStudents as $student) {
                if ($student->zone && isset($dataByZone[$student->zone->zone_id])) {
                    $dataByZone[$student->zone->zone_id]['unassignedStudents'][] = [
                        'student_reg_no' => $student->student_reg_no,
                        'name' => $student->name,
                        'school_name' => $student->school ? $student->school->school_name : 'Unknown School',
                    ];
                }
            }

            // Distribute assigned students by zone
            foreach ($assignedStudents as $student) {
                if ($student->zone && isset($dataByZone[$student->zone->zone_id])) {
                    $assignment = StudentSupervisorAssignment::findOne(['student_reg_no' => $student->student_reg_no]);
                    $supervisor = null;
                    if ($assignment && $assignment->supervisor_user_id) {
                        $supervisor = Users::findOne(['user_id' => $assignment->supervisor_user_id]);
                    }

                    $dataByZone[$student->zone->zone_id]['assignedStudents'][] = [
                        'student_reg_no' => $student->student_reg_no,
                        'name' => $student->name,
                        'school_name' => $student->school ? $student->school->school_name : 'Unknown School',
                        'supervisor_name' => $supervisor ? $supervisor->name : 'Unknown',
                        'supervisor_user_id' => $supervisor ? $supervisor->user_id : null,
                    ];
                }
            }

            $data['dataByZone'] = array_values($dataByZone);
        }

        return $data;
    }
}
