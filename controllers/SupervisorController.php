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
 * SupervisorController handles supervisor-specific operations
 */
class SupervisorController extends Controller
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
                            // Only allow supervisors (role_id = 1)
                            $user = Yii::$app->user->identity;
                            return $user && $user->role_id == 1;
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
     * Display supervisor profile
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity; // Get current logged-in supervisor

        // Get basic user info
        $supervisor = Users::findOne(['user_id' => $user->user_id]);

        // Get assessment statistics for this supervisor
        $totalAssessments = Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->count();

        $pendingAssessments = Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->andWhere(['is', 'overall_level', null]) // NULL overall_level = incomplete
            ->count();

        $completedAssessments = $totalAssessments - $pendingAssessments;

        // Get unique schools where supervisor conducted assessments
        $schools = Assessment::find()
            ->select(['school_id'])
            ->where(['examiner_user_id' => $user->user_id])
            ->distinct()
            ->column();

        $schoolCount = count($schools);

        // Get unique students assessed
        $uniqueStudents = Assessment::find()
            ->select(['student_reg_no'])
            ->where(['examiner_user_id' => $user->user_id])
            ->distinct()
            ->count('DISTINCT student_reg_no');

        // Get total grades for this supervisor's assessments
        $totalGrades = \app\models\Grade::find()
            ->leftJoin('assessment', 'grade.assessment_id = assessment.assessment_id')
            ->where(['assessment.examiner_user_id' => $user->user_id])
            ->count();

        // Get schools assigned to this supervisor and count assigned students per school
        $assignedSchools = [];
        $schoolStudentCounts = [];
        $assignments = StudentSupervisorAssignment::find()
            ->with(['student.school', 'zone'])
            ->where(['supervisor_user_id' => $user->user_id])
            ->all();

        foreach ($assignments as $assignment) {
            if ($assignment->student && $assignment->student->school) {
                $schoolId = $assignment->student->school->school_id;
                $assignedSchools[$schoolId] = $assignment->student->school;

                if (!isset($schoolStudentCounts[$schoolId])) {
                    $schoolStudentCounts[$schoolId] = 0;
                }
                $schoolStudentCounts[$schoolId]++;
            }
        }

        $assignedSchoolCount = count($assignedSchools);

        // Get unique learning areas assessed by this supervisor
        $learningAreas = Assessment::find()
            ->select(['learning_area_id'])
            ->where(['examiner_user_id' => $user->user_id])
            ->andWhere(['is not', 'learning_area_id', null])
            ->distinct()
            ->count('DISTINCT learning_area_id');

        // Setup search model for assessments
        $searchModel = new AssessmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Get the query object from the data provider and add supervisor filter
        $dataProvider->query->andWhere(['examiner_user_id' => $user->user_id])
                            ->andWhere(['or', ['archived' => 0], ['archived' => null]]);
        
        // Order by date descending and limit to recent
        $dataProvider->query->orderBy(['assessment_date' => SORT_DESC])
              ->limit(20); // Show more if searching
        
        $recentAssessments = $dataProvider->getModels();

        // Get supervisor's role name
        $role = $supervisor ? $supervisor->role : null;

        // Get all students assigned to this supervisor
        $assignedStudents = [];
        $assignments = StudentSupervisorAssignment::find()
            ->where(['supervisor_user_id' => $user->user_id])
            ->all();

        foreach ($assignments as $assignment) {
            $student = \app\models\Students::findOne(['student_reg_no' => $assignment->student_reg_no]);
            if ($student) {
                $assignedStudents[] = $student;
            }
        }

        return $this->render('supervisor-profile', [
            'supervisor' => $supervisor,
            'role' => $role,
            'totalAssessments' => $totalAssessments,
            'pendingAssessments' => $pendingAssessments,
            'completedAssessments' => $completedAssessments,
            'schoolCount' => $schoolCount,
            'uniqueStudents' => $uniqueStudents,
            'totalGrades' => $totalGrades,
            'learningAreas' => $learningAreas,
            'recentAssessments' => $recentAssessments,
            'searchModel' => $searchModel,
            'assignedStudents' => $assignedStudents,
            'assignedSchools' => $assignedSchools,
            'assignedSchoolCount' => $assignedSchoolCount,
            'schoolStudentCounts' => $schoolStudentCounts,
        ]);
    }

    /**
     * Edit supervisor profile
     */
    public function actionEdit()
    {
        $user = Yii::$app->user->identity;
        $supervisor = Users::findOne(['user_id' => $user->user_id]);

        if ($supervisor->load(Yii::$app->request->post()) && $supervisor->save()) {
            Yii::$app->session->setFlash('success', 'Profile updated successfully!');
            return $this->redirect(['profile']);
        }

        return $this->render('edit-supervisor-profile', [
            'model' => $supervisor,
        ]);
    }

    /**
     * Step 1: Select student for assessment
     */
    public function actionSelectStudent()
    {
        $user = Yii::$app->user->identity;
        $searchQuery = Yii::$app->request->get('search', '');
        $students = [];

        // Get all students assigned to this supervisor

        $assignedStudentsQuery = StudentSupervisorAssignment::find()
            ->select('student_reg_no')
            ->where(['supervisor_user_id' => $user->user_id])
            ->andWhere(['!=', 'status', 'assessed']);

        if (!empty($searchQuery)) {
            $assignedStudentsQuery->andWhere(['ilike', 'student_reg_no', '%' . $searchQuery . '%']);
        }

        $assignedStudentRegNos = $assignedStudentsQuery
            ->orderBy(['student_reg_no' => SORT_ASC])
            ->column();

        // Filter out students who have already been assessed by this supervisor
        $assessedStudentRegNos = \app\models\Assessment::find()
            ->select('student_reg_no')
            ->where(['examiner_user_id' => $user->user_id])
            ->andWhere(['not', ['overall_level' => null]]) // Only completed assessments
            ->column();

        $students = array_values(array_diff($assignedStudentRegNos, $assessedStudentRegNos));

        if (Yii::$app->request->isPost) {
            $studentRegNo = Yii::$app->request->post('student_reg_no');
            $assignment = StudentSupervisorAssignment::findOne([
                'student_reg_no' => $studentRegNo,
                'supervisor_user_id' => $user->user_id,
            ]);

            if (!$assignment) {
                Yii::$app->session->setFlash('error', 'This student is not assigned to you. Please ask TP Office to assign the student first.');
                return $this->redirect(['select-student']);
            }

            if (!empty($studentRegNo)) {
                // Create or resume assessment for this student
                $existingAssessment = Assessment::find()
                    ->where(['examiner_user_id' => $user->user_id, 'student_reg_no' => $studentRegNo])
                    ->andWhere(['or', ['overall_level' => null], ['overall_level' => '']])
                    ->andWhere(['or', ['archived' => 0], ['archived' => null]])
                    ->orderBy(['assessment_date' => SORT_DESC])
                    ->one();

                if ($existingAssessment) {
                    Yii::$app->session->setFlash('info', 'Resuming your existing incomplete assessment.');
                    return $this->redirect(['/assessment/view', 'assessment_id' => $existingAssessment->assessment_id]);
                }

                // Create new assessment for this student
                $assessment = new Assessment();
                $assessment->examiner_user_id = $user->user_id;
                $assessment->student_reg_no = $studentRegNo;
                $assessment->assessment_date = date('Y-m-d');

                $assignment = StudentSupervisorAssignment::find()
                    ->with('student.school')
                    ->where(['supervisor_user_id' => $user->user_id, 'student_reg_no' => $studentRegNo])
                    ->one();

                if ($assignment && $assignment->student && $assignment->student->school_id) {
                    $assessment->school_id = $assignment->student->school_id;
                } else {
                    $school = School::find()->one();
                    if ($school) {
                        $assessment->school_id = $school->school_id;
                    } else {
                        Yii::$app->session->setFlash('error', 'No school found. Please contact administrator.');
                        return $this->redirect(['select-student']);
                    }
                }

                $assessment->archived = 0; // ensure new assessments are visible as in-progress

                if ($assessment->save()) {
                    // Mark student as assessed in assignment
                    if ($assignment) {
                        $assignment->status = 'assessed';
                        $assignment->save(false);
                    }
                    Yii::$app->session->setFlash('success', 'Assessment created. You can now review or update details.');
                    return $this->redirect(['/assessment/update', 'assessment_id' => $assessment->assessment_id]);
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to create assessment.');
                }
            }
        }

        return $this->render('select-student', [
            'students' => $students,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Create or resume an assessment for a student assigned to this supervisor.
     */
    public function actionAssessStudent($student_reg_no)
    {
        // Check if student is already assessed
        $assignment = StudentSupervisorAssignment::findOne([
            'student_reg_no' => $student_reg_no,
            'supervisor_user_id' => Yii::$app->user->id
        ]);
        if ($assignment && $assignment->status === 'assessed') {
            Yii::$app->session->setFlash('info', 'This student has already been assessed.');
            return $this->redirect(['profile']);
        }

        $user = Yii::$app->user->identity;
        $assignment = StudentSupervisorAssignment::find()
            ->with('student.school')
            ->where(['supervisor_user_id' => $user->user_id, 'student_reg_no' => $student_reg_no])
            ->one();

        if (!$assignment || !$assignment->student) {
            Yii::$app->session->setFlash('error', 'This student is not assigned to you.');
            return $this->redirect(['profile']);
        }

        // Get student details to pre-fill form
        $schoolId = $assignment->student->school_id ? $assignment->student->school_id : null;
        $assessmentDate = date('Y-m-d');

        // Redirect to assessment create form with student and supervisor details pre-filled
        return $this->redirect([
            '/assessment/create', 
            'student_reg_no' => $student_reg_no,
            'examiner_user_id' => $user->user_id,
            'school_id' => $schoolId,
            'assessment_date' => $assessmentDate
        ]);
    }

    /**
     * View students assigned to this supervisor for a specific school
     */
    public function actionSchoolStudents($school_id)
    {
        $user = Yii::$app->user->identity;
        
        // Get the school
        $school = School::findOne(['school_id' => $school_id]);
        if (!$school) {
            throw new \yii\web\NotFoundHttpException('School not found.');
        }

        // Get students assigned to this supervisor in this school
        $assignments = StudentSupervisorAssignment::find()
            ->with(['student.school'])
            ->where(['supervisor_user_id' => $user->user_id])
            ->all();

        $schoolStudents = [];
        foreach ($assignments as $assignment) {
            if ($assignment->student && $assignment->student->school_id == $school_id) {
                $schoolStudents[] = $assignment->student;
            }
        }

        return $this->render('school-students', [
            'school' => $school,
            'students' => $schoolStudents,
        ]);
    }

    /**
     * Get profile data for real-time updates (AJAX)
     */
    public function actionGetProfileData()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 1) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $lastUpdate = Yii::$app->request->get('last_update', 0);

        // Get current statistics
        $totalAssessments = Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->count();

        $completedAssessments = Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->andWhere(['is not', 'overall_level', null])
            ->count();

        $inProgressAssessments = Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->andWhere(['is', 'overall_level', null])
            ->count();

        // Check for updates
        $newAssessmentsCount = Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->andWhere(['>', 'assessment_date', date('Y-m-d H:i:s', $lastUpdate/1000)])
            ->count();

        $updated = $newAssessmentsCount > 0;

        return [
            'updated' => $updated,
            'totalAssessments' => $totalAssessments,
            'completedAssessments' => $completedAssessments,
            'inProgressAssessments' => $inProgressAssessments,
            'timestamp' => time() * 1000
        ];
    }
}

