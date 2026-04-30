<?php

namespace app\controllers;

use app\models\Assessment;
use app\models\AssessmentSearch;
use app\components\RbacHelper;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AssessmentController implements the CRUD actions for Assessment model.
 */
class AssessmentController extends Controller
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
                            'roles' => ['@'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['save-grid'], // Temporarily allow unauthenticated access for testing
                            'roles' => ['?'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'save-grid' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        if ($action->id === 'save-grid') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all Assessment models.
     * Supervisors see own assessments, Coordinators/TpOffice/Chair see all
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AssessmentSearch();
        
        // Role-based filtering
        if (RbacHelper::isSupervisor()) {
            // Supervisors only see assessments they created
            $query = Assessment::find();
            $searchModel->examiner_user_id = Yii::$app->user->id;
        } elseif (RbacHelper::isZoneCoordinator()) {
            // Zone Coordinators see assessments from schools in their zone
            $user = Yii::$app->user->identity;
            if ($user->zone_id) {
                $searchModel->zone_id = $user->zone_id; // Assuming AssessmentSearch has zone_id filter
            }
        } elseif (RbacHelper::isDepartmentChair()) {
            // Department Chairs see assessments from their school
            $user = Yii::$app->user->identity;
            if ($user->school_id) {
                $searchModel->school_id = $user->school_id;
            }
        }
        // TP Office sees all
        
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Assessment model.
     * @param int $assessment_id Assessment ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($assessment_id = null, $id = null)
    {
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = $this->findModel($assessmentId);

        // Supervisor view policy
        if (RbacHelper::isSupervisor()) {
            // If no owner yet, claim assessment for continuity
            if ($model->examiner_user_id === null) {
                $model->examiner_user_id = Yii::$app->user->id;
                $model->save(false);
                Yii::$app->session->setFlash('info', 'This assessment was unassigned and has now been claimed to your profile for completion.');
            }

            // If non-owner and in-progress, allow reassign to current supervisor (workflow safety)
            if ($model->examiner_user_id !== Yii::$app->user->id && $model->archived != Assessment::STATUS_SUBMITTED) {
                $model->examiner_user_id = Yii::$app->user->id;
                $model->save(false);
                Yii::$app->session->setFlash('info', 'You are now assigned as the examiner for this in-progress assessment.');
            }

            // For submitted assessments, only assigned examiner may view
            if ($model->examiner_user_id !== Yii::$app->user->id && $model->archived == Assessment::STATUS_SUBMITTED) {
                throw new ForbiddenHttpException('You do not have permission to view this assessment.');
            }
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Upload assessment images without full update page
     * @param int $assessment_id Assessment ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUploadImages($assessment_id = null, $id = null)
    {
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = $this->findModel($assessmentId);

        if (RbacHelper::isSupervisor() && $model->validated_by !== null) {
            throw new ForbiddenHttpException('This assessment has already been validated and cannot be modified.');
        }

        // ACL: supervisors can only work on own/in-progress assessments; others must be mapped indirectly
        if (RbacHelper::isSupervisor() && $model->examiner_user_id !== null && $model->examiner_user_id !== Yii::$app->user->id && $model->archived == Assessment::STATUS_SUBMITTED) {
            throw new ForbiddenHttpException('You do not have permission to upload images for this assessment.');
        }

        if (Yii::$app->request->isPost) {
            $uploadedFiles = \yii\web\UploadedFile::getInstancesByName('images');
            try {
                $savedFiles = \app\components\AssessmentImageBehavior::uploadImages($model, $uploadedFiles);
                if (!empty($savedFiles)) {
                    Yii::$app->session->setFlash('success', 'Uploaded ' . count($savedFiles) . ' image(s) successfully.');
                } else {
                    Yii::$app->session->setFlash('info', 'No new images were uploaded (limit may have been reached).');
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Image upload failed: ' . $e->getMessage());
            }

            return $this->redirect(['view', 'assessment_id' => $model->assessment_id]);
        }

        return $this->render('upload-images', [
            'model' => $model,
        ]);
    }

    /**
     * Display student report (feedback only, no marks)
     * @param int $assessment_id Assessment ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReportStudent($assessment_id = null, $id = null)
    {
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = $this->findModel($assessmentId);
        
        return $this->render('report', [
            'model' => $model,
            'showMarks' => false,
        ]);
    }

    /**
     * Display office report (with marks and total score)
     * @param int $assessment_id Assessment ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReportOffice($assessment_id = null, $id = null)
    {
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = $this->findModel($assessmentId);
        
        // Only allow Coordinators, TP Office, and Department Chair to view office report
        if (RbacHelper::isSupervisor() && $model->examiner_user_id !== null && $model->examiner_user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('You do not have permission to view this report.');
        }

        if (RbacHelper::isSupervisor() && $model->examiner_user_id === null) {
            $model->examiner_user_id = Yii::$app->user->id;
            $model->save(false);
        }
        
        return $this->render('report', [
            'model' => $model,
            'showMarks' => true,
        ]);
    }

    /**
     * Creates a new Assessment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($student_reg_no = null, $examiner_user_id = null, $school_id = null, $assessment_date = null)
    {
        if (!RbacHelper::isSupervisor()) {
            throw new ForbiddenHttpException('Only Supervisors can create assessments.');
        }

        $model = new Assessment();

        if ($this->request->isPost) {
            $model->examiner_user_id = Yii::$app->user->id; // Ensure supervisor ownership is recorded
            if ($model->load($this->request->post()) && $model->save()) {
                // Send notification for student selection
                \app\components\NotificationService::notifyStudentSelected($model);
                // Store in session for navigation tracking
                Yii::$app->session['active_assessment_id'] = $model->assessment_id;
                Yii::$app->session['active_assessment_student'] = $model->student_reg_no;
                // Redirect to update form, not grade grid
                return $this->redirect(['update', 'assessment_id' => $model->assessment_id]);
            }
        } else {
            $model->loadDefaultValues();
            
            // Pre-fill form fields from supervisor profile
            if ($student_reg_no) {
                $model->student_reg_no = $student_reg_no;
                $student = \app\models\Students::findOne(['student_reg_no' => $student_reg_no]);
                if ($student) {
                    if (!$model->school_id && $student->school_id) {
                        $model->school_id = $student->school_id;
                    }
                    if ($student->class_id) {
                        $model->class_id = $student->class_id;
                    }
                }
            }
            if ($examiner_user_id) {
                $model->examiner_user_id = $examiner_user_id;
            }
            if ($school_id) {
                $model->school_id = $school_id;
            }
            if ($assessment_date) {
                $model->assessment_date = $assessment_date;
            }
        }

        $competenceAreas = \app\models\CompetenceArea::find()->orderBy(['competence_id' => SORT_ASC])->all();

        return $this->render('create', [
            'model' => $model,
            'competenceAreas' => $competenceAreas,
        ]);
    }

    /**
     * Updates an existing Assessment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $assessment_id Assessment ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($assessment_id = null, $id = null)
    {
        if (RbacHelper::isZoneCoordinator()) {
            throw new ForbiddenHttpException('Zone Coordinators must use their dedicated review workflow instead of direct assessment update.');
        }

        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = $this->findModel($assessmentId);

        if (RbacHelper::isSupervisor() && $model->validated_by !== null) {
            Yii::$app->session->setFlash('info', 'Assessment completed. Editing is not allowed.');
            return $this->redirect(['view', 'assessment_id' => $model->assessment_id]);
        }
        
        // Store in session for navigation tracking
        Yii::$app->session['active_assessment_id'] = $model->assessment_id;
        Yii::$app->session['active_assessment_student'] = $model->student_reg_no;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // Handle grade submissions
                $gradesData = Yii::$app->request->post('grades', []);
                if (!empty($gradesData)) {
                    try {
                        foreach ($gradesData as $competenceId => $gradeData) {
                            if (!empty($gradeData['score']) || !empty($gradeData['level'])) {
                                $gradeId = $gradeData['grade_id'] ?? null;
                                
                                if ($gradeId) {
                                    // Update existing grade
                                    $grade = \app\models\Grade::findOne($gradeId);
                                    if (!$grade) {
                                        $grade = new \app\models\Grade();
                                        $grade->assessment_id = $model->assessment_id;
                                        $grade->competence_id = $competenceId;
                                    }
                                } else {
                                    // Create new grade
                                    $grade = new \app\models\Grade();
                                    $grade->assessment_id = $model->assessment_id;
                                    $grade->competence_id = $competenceId;
                                }

                                $grade->score = $gradeData['score'] ?? null;
                                $grade->level = $gradeData['level'] ?? null;
                                $grade->remarks = $gradeData['remarks'] ?? '';

                                if (!$grade->save()) {
                                    Yii::$app->session->addFlash('warning', 'Assessment saved but error saving grade for competence ' . $competenceId . ': ' . json_encode($grade->getErrors()));
                                }
                            }
                        }

                        // Recalculate assessment totals after grading
                        $model->refresh(); // Refresh to get updated relations
                        $model->total_score = $model->calculateTotalScore();
                        $model->overall_level = $model->classifyOverallLevel($model->total_score);
                        $model->save(false);

                        // Send notification for completed grades
                        \app\components\NotificationService::notifyGradesComplete($model);
                        
                        Yii::$app->session->addFlash('success', 'Assessment and grades saved successfully.');
                    } catch (\Exception $e) {
                        Yii::$app->session->addFlash('warning', 'Assessment saved but error processing grades: ' . $e->getMessage());
                    }
                }

                // Handle image uploads (TP Office may not upload evidence)
                if (!\app\components\RbacHelper::isTpOffice()) {
                    $images = \yii\web\UploadedFile::getInstancesByName('images');
                    if (!empty($images)) {
                        try {
                            \app\components\AssessmentImageBehavior::uploadImages($model, $images);
                            // Send notification for evidence upload
                            \app\components\NotificationService::notifyEvidenceUploaded($model, count($images));
                        } catch (\Exception $e) {
                            Yii::$app->session->addFlash('warning', 'Assessment saved but error uploading images: ' . $e->getMessage());
                        }
                    }
                } else {
                    // Explicit feedback for TP Office users
                    Yii::$app->session->addFlash('info', 'TP Office users cannot upload evidence images.');
                }
                
                return $this->redirect(['view', 'assessment_id' => $model->assessment_id]);
            }
        }

        $competenceAreas = \app\models\CompetenceArea::find()->orderBy(['competence_id' => SORT_ASC])->all();

        return $this->render('update', [
            'model' => $model,
            'competenceAreas' => $competenceAreas,
        ]);
    }

    /**
     * Deletes an existing Assessment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $assessment_id Assessment ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($assessment_id = null, $id = null)
    {
        if (RbacHelper::isZoneCoordinator()) {
            throw new ForbiddenHttpException('Zone Coordinators are not allowed to delete assessments.');
        }

        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $this->findModel($assessmentId)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Submit the completed assessment report
     * @param int $assessment_id
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSubmit($assessment_id = null, $id = null)
    {        if (RbacHelper::isZoneCoordinator()) {
            throw new ForbiddenHttpException('Zone Coordinators cannot submit assessments.');
        }
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = $this->findModel($assessmentId);

        // Enforce expected competency count before submission (12 is expected for TP template).
        $requiredCompetenceCount = \app\models\CompetenceArea::find()->count();
        if (count($model->grades) < $requiredCompetenceCount) {
            Yii::$app->session->setFlash('warning', 'Please complete all ' . $requiredCompetenceCount . ' competence grades before submitting the report.');
            return $this->redirect(['view', 'assessment_id' => $model->assessment_id]);
        }

        // Ensure totals and overall level are computed before submission.
        $model->total_score = $model->calculateTotalScore();
        $model->overall_level = $model->classifyOverallLevel($model->total_score);
        $model->validated_by = null;
        $model->validated_at = null;
        $model->archived = Assessment::STATUS_SUBMITTED;
        $model->save(false);

        \app\components\NotificationService::notifyAssessmentCompleted($model);
        
        // Clear active assessment session when submitting
        unset(Yii::$app->session['active_assessment_id']);
        unset(Yii::$app->session['active_assessment_student']);

        Yii::$app->session->setFlash('success', 'Assessment report submitted successfully.');
        return $this->redirect(['view', 'assessment_id' => $model->assessment_id]);
    }

    /**
     * Delete an image from assessment
     * @param int $assessment_id Assessment ID
     * @param string $filename Image filename
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteImage($assessment_id, $filename)
    {
        if (\app\components\RbacHelper::isTpOffice()) {
            throw new ForbiddenHttpException('TP Office users are not permitted to modify evidence images.');
        }

        $model = $this->findModel($assessment_id);

        if (RbacHelper::isSupervisor() && $model->validated_by !== null) {
            throw new ForbiddenHttpException('This assessment has already been validated and cannot be modified.');
        }
        
        \app\components\AssessmentImageBehavior::deleteImage($assessment_id, $filename);
        
        Yii::$app->session->addFlash('success', 'Image deleted successfully');
        return $this->redirect(['update', 'assessment_id' => $assessment_id]);
    }

    /**
     * Displays grading grid for 12 competence areas
     * @param int $assessment_id Assessment ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGradeGrid($assessment_id = null, $id = null)
    {        if (RbacHelper::isZoneCoordinator()) {
            throw new ForbiddenHttpException('Zone Coordinators cannot access the grading grid.');
        }
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = $this->findModel($assessmentId);

        if (RbacHelper::isSupervisor() && $model->validated_by !== null) {
            Yii::$app->session->setFlash('info', 'Assessment completed. Editing is not allowed.');
            return $this->redirect(['view', 'assessment_id' => $model->assessment_id]);
        }
        
        // Store in session for navigation tracking
        Yii::$app->session['active_assessment_id'] = $model->assessment_id;
        Yii::$app->session['active_assessment_student'] = $model->student_reg_no;

        $competenceAreas = \app\models\CompetenceArea::find()
            ->orderBy(['competence_id' => SORT_ASC])
            ->all();

        $existingGrades = [];
        foreach ($model->grades as $grade) {
            $existingGrades[$grade->competence_id] = [
                'grade_id' => $grade->grade_id,
                'score' => $grade->score,
                'level' => $grade->level,
                'remarks' => $grade->remarks,
            ];
        }

        return $this->render('grade-grid', [
            'model' => $model,
            'competenceAreas' => $competenceAreas,
            'existingGrades' => $existingGrades,
        ]);
    }

    /**
     * Display audit log for assessment
     * @param int $assessment_id Assessment ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAuditLog($assessment_id = null, $id = null)
    {
        $assessmentId = $assessment_id ?? $id ?? Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        if (!$assessmentId) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        $model = $this->findModel($assessmentId);
        
        return $this->render('audit-log', [
            'model' => $model,
        ]);
    }

    /**
     * Save multiple grades at once (AJAX endpoint)
     * @return array JSON response
     */
    public function actionSaveGrid()
    {
        // Allow both POST with CSRF and raw JSON requests
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Check if request is POST
        if (!Yii::$app->request->isPost && !Yii::$app->request->isAjax) {
            return ['success' => false, 'message' => 'Invalid request method. Expected POST.'];
        }

        // Get raw body for JSON or POST data
        $input = [];
        if (Yii::$app->request->isAjax && Yii::$app->request->getContentType() === 'application/json') {
            $input = json_decode(Yii::$app->request->getRawBody(), true);
        } else {
            $input = Yii::$app->request->post();
        }

        if (empty($input)) {
            return ['success' => false, 'message' => 'No data received. Request body is empty.'];
        }

        $assessmentId = $input['assessmentId'] ?? null;
        $gradesData = $input['grades'] ?? [];

        if (!$assessmentId) {
            return ['success' => false, 'message' => 'Assessment ID is required. Received: ' . json_encode($input)];
        }

        if (empty($gradesData)) {
            return ['success' => false, 'message' => 'No grades data received.'];
        }

        // Get the assessment
        try {
            $assessment = $this->findModel($assessmentId);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Assessment not found: ' . $e->getMessage()];
        }

        try {
            // Save each grade
            $savedCount = 0;
            foreach ($gradesData as $gradeData) {
                $gradeId = $gradeData['gradeId'] ?? null;
                
                if ($gradeId) {
                    // Update existing grade
                    $grade = \app\models\Grade::findOne($gradeId);
                    if (!$grade) {
                        continue;
                    }
                } else {
                    // Create new grade
                    $grade = new \app\models\Grade();
                    $grade->assessment_id = $assessmentId;
                    $grade->competence_id = $gradeData['competenceId'];
                }

                $grade->score = $gradeData['score'];
                $grade->level = $gradeData['level'];
                $grade->remarks = $gradeData['remarks'] ?? '';

                if (!$grade->save()) {
                    return ['success' => false, 'message' => 'Error saving grade for competence ' . $gradeData['competenceId'] . ': ' . json_encode($grade->getErrors())];
                }
                $savedCount++;
            }

            // Recalculate assessment totals
            $assessment->total_score = $assessment->calculateTotalScore();
            $assessment->overall_level = $assessment->classifyOverallLevel($assessment->total_score);
            $assessment->save(false);

            // Send notification for completed grades
            \app\components\NotificationService::notifyGradesComplete($assessment);

            return ['success' => true, 'message' => "Successfully saved $savedCount grades."];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get classes for a specific school (AJAX)
     * @param int $school_id School ID
     * @return array JSON array of classes
     */
    public function actionGetClasses($school_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$school_id) {
            return ['classes' => []];
        }

        $classes = \app\models\SchoolClass::find()
            ->where(['school_id' => $school_id])
            ->orderBy(['class_name' => SORT_ASC])
            ->all();

        $result = [];
        foreach ($classes as $class) {
            $result[$class->class_id] = $class->class_name;
        }

        return ['classes' => $result];
    }

    /**
     * Get student details for a specific student registration number (AJAX)
     * @param string $student_reg_no Student registration number
     * @return array JSON student details and class options
     */
    public function actionGetStudentDetails($student_reg_no)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$student_reg_no) {
            return ['student' => null, 'classes' => []];
        }

        $student = \app\models\Students::findOne(['student_reg_no' => $student_reg_no]);
        if (!$student) {
            return ['student' => null, 'classes' => []];
        }

        $schoolId = $student->school_id;
        $classId = $student->class_id;

        if (!$schoolId && $classId) {
            $class = \app\models\SchoolClass::findOne($classId);
            if ($class) {
                $schoolId = $class->school_id;
            }
        }

        $classes = [];
        if ($schoolId) {
            $classRecords = \app\models\SchoolClass::find()
                ->where(['school_id' => $schoolId])
                ->orderBy(['class_name' => SORT_ASC])
                ->all();
            foreach ($classRecords as $classRecord) {
                $classes[$classRecord->class_id] = $classRecord->class_name;
            }
        }

        return [
            'student' => [
                'school_id' => $schoolId,
                'class_id' => $classId,
            ],
            'classes' => $classes,
        ];
    }

    /**
     * Get strands for a specific learning area (AJAX)
     * @param int $learning_area_id Learning Area ID
     * @return array JSON array of strands
     */
    public function actionGetStrands($learning_area_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$learning_area_id) {
            return ['strands' => []];
        }

        $strands = \app\models\Strand::find()
            ->where(['learning_area_id' => $learning_area_id])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $result = [];
        foreach ($strands as $strand) {
            $result[$strand->strand_id] = $strand->name;
        }

        return ['strands' => $result];
    }

    /**
     * Get strands for a specific learning area (AJAX)
     * @param int $learning_area_id Learning Area ID
     * @return array JSON array of strands
     */
    public function actionGetSubstrands($strand_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$strand_id) {
            return ['substrands' => []];
        }

        $substrands = \app\models\Substrand::find()
            ->where(['strand_id' => $strand_id])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $result = [];
        foreach ($substrands as $substrand) {
            $result[$substrand->substrand_id] = $substrand->name;
        }

        return ['substrands' => $result];
    }

    /**
     * Finds the Assessment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $assessment_id Assessment ID
     * @return Assessment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($assessment_id)
    {
        if (($model = Assessment::findOne(['assessment_id' => $assessment_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
