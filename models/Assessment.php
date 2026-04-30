<?php

namespace app\models;

use Yii;
use app\models\SchoolClass;

/**
 * This is the model class for table "assessment".
 *
 * @property int $assessment_id
 * @property int $examiner_user_id
 * @property string $student_reg_no
 * @property int $school_id
 * @property int|null $class_id
 * @property int|null $learning_area_id
 * @property int|null $strand_id
 * @property int|null $substrand_id
 * @property string $assessment_date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int|null $total_score
 * @property string|null $overall_level
 * @property int $archived
 * @property string|null $archived_at
 * @property int|null $validated_by
 * @property string|null $validated_at
 *
 * @property CompetenceArea[] $competences
 * @property Users $examinerUser
 * @property Users $validatorUser
 * @property Grade[] $grades
 * @property LearningArea $learningArea
 * @property Strand $strand
 * @property Substrand $substrand
 * @property School $school
 * @property SchoolClass $schoolClass
 * @property Students $student
 */
class Assessment extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'assessment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['learning_area_id', 'class_id', 'start_time', 'end_time', 'total_score', 'overall_level', 'archived_at', 'validated_by', 'validated_at', 'strand_id', 'substrand_id'], 'default', 'value' => null],
            [['examiner_user_id', 'student_reg_no', 'school_id', 'class_id', 'learning_area_id', 'assessment_date'], 'required'],
            [['examiner_user_id', 'school_id', 'class_id', 'learning_area_id', 'strand_id', 'substrand_id', 'total_score', 'archived', 'validated_by'], 'default', 'value' => null],
            [['examiner_user_id', 'school_id', 'class_id', 'learning_area_id', 'strand_id', 'substrand_id', 'total_score', 'archived', 'validated_by'], 'integer'],
            [['assessment_date', 'start_time', 'end_time', 'archived_at', 'validated_at'], 'safe'],
            [['student_reg_no'], 'string', 'max' => 50],
            [['overall_level'], 'string', 'max' => 5],
            [['learning_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => LearningArea::class, 'targetAttribute' => ['learning_area_id' => 'learning_area_id']],
            [['strand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Strand::class, 'targetAttribute' => ['strand_id' => 'strand_id']],
            [['substrand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Substrand::class, 'targetAttribute' => ['substrand_id' => 'substrand_id']],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::class, 'targetAttribute' => ['school_id' => 'school_id']],
            [['class_id'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolClass::class, 'targetAttribute' => ['class_id' => 'class_id']],
            [['examiner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['examiner_user_id' => 'user_id']],
            [['validated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['validated_by' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'assessment_id' => 'Assessment',
            'examiner_user_id' => 'Examiner',
            'student_reg_no' => 'Student Reg No',
            'school_id' => 'School',
            'class_id' => 'Class',
            'learning_area_id' => 'Learning Area',
            'strand_id' => 'Strand',
            'substrand_id' => 'Substrand',
            'assessment_date' => 'Assessment Date',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'total_score' => 'Total Score',
            'overall_level' => 'Overall Level',
            'archived' => 'Archived',
            'archived_at' => 'Archived At',
            'validated_by' => 'Validated By',
            'validated_at' => 'Validated At',
        ];
    }

    /**
     * Gets query for [[Competences]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompetences()
    {
        return $this->hasMany(CompetenceArea::class, ['competence_id' => 'competence_id'])->viaTable('grade', ['assessment_id' => 'assessment_id']);
    }

    /**
     * Gets query for [[ExaminerUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExaminerUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'examiner_user_id']);
    }

    /**
     * Gets query for [[ValidatorUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getValidatorUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'validated_by']);
    }

    /**
     * Alias for getExaminerUser() to allow $model->examiner access
     */
    public function getExaminer()
    {
        return $this->examinerUser;
    }

    /**
     * Gets query for [[Grades]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGrades()
    {
        return $this->hasMany(Grade::class, ['assessment_id' => 'assessment_id']);
    }

    /**
     * Gets query for [[LearningArea]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLearningArea()
    {
        return $this->hasOne(LearningArea::class, ['learning_area_id' => 'learning_area_id']);
    }

    /**
     * Gets query for [[Strand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStrand()
    {
        return $this->hasOne(Strand::class, ['strand_id' => 'strand_id']);
    }

    /**
     * Gets query for [[Substrand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubstrand()
    {
        return $this->hasOne(Substrand::class, ['substrand_id' => 'substrand_id']);
    }

    /**
     * Gets query for [[School]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchool()
    {
        return $this->hasOne(School::class, ['school_id' => 'school_id']);
    }

    /**
     * Gets query for [[SchoolClass]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolClass()
    {
        return $this->hasOne(SchoolClass::class, ['class_id' => 'class_id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Students::class, ['student_reg_no' => 'student_reg_no']);
    }

    /**
     * Calculate total score from all grades (max 100)
     * Each of 10 competence areas scored 0-10, total out of 100
     */
    public function calculateTotalScore()
    {
        $grades = $this->getGrades()->all();
        $totalScore = 0;
        
        foreach ($grades as $grade) {
            if ($grade->score !== null) {
                $totalScore += $grade->score;
            }
        }
        
        return $totalScore;
    }

    /**
     * Classify overall level based on total score
     * Based on TP E24 grading scale:
     * BE: 0-39 (Below Expectations)
     * AE: 40-54 (Approaching Expectations)
     * ME: 55-79 (Meets Expectations)
     * EE: 80-100 (Exceeds Expectations)
     */
    public function classifyOverallLevel($totalScore)
    {
        if ($totalScore >= 80 && $totalScore <= 100) {
            return Grade::LEVEL_EE;
        } elseif ($totalScore >= 55 && $totalScore < 80) {
            return Grade::LEVEL_ME;
        } elseif ($totalScore >= 40 && $totalScore < 55) {
            return Grade::LEVEL_AE;
        } else {
            return Grade::LEVEL_BE;
        }
    }

    const STATUS_DRAFT = 0;
    const STATUS_SUBMITTED = 1;

    /**
     * Get human-readable assessment status
     * @return string
     */
    public function getStatusLabel()
    {
        if ($this->validated_by !== null) {
            return 'Completed';
        }
        return $this->archived == self::STATUS_SUBMITTED ? 'Submitted' : 'In Progress';
    }

    /**
     * Helper for completed status check
     * @return bool
     */
    public function getIsCompleted()
    {
        return $this->validated_by !== null;
    }

    /**
     * Helper for status check
     * @return bool
     */
    public function getIsSubmitted()
    {
        return $this->archived == self::STATUS_SUBMITTED;
    }

    /**
     * Auto-compute total score and overall level before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Only recalculate if not already set (to avoid overriding manual calculations)
            if ($this->total_score === null) {
                // Recalculate total score
                $this->total_score = $this->calculateTotalScore();
                
                // Classify overall level
                if ($this->total_score !== null) {
                    $this->overall_level = $this->classifyOverallLevel($this->total_score);
                }
            }
            
            return true;
        }
        return false;
    }

    /**
     * Log audit events after save
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Log to audit trail
        $action = $insert ? 'create' : 'update';
        $changes = [];
        
        if (!$insert) {
            foreach ($changedAttributes as $attribute => $oldValue) {
                $changes[$attribute] = [
                    'old' => $oldValue,
                    'new' => $this->getAttribute($attribute)
                ];
            }
        }
        
        \app\components\AuditLogger::log(
            $action,
            'assessment',
            $this->assessment_id,
            $changes,
            $insert ? 'Assessment created' : 'Assessment updated'
        );

        // Send notifications
        if ($insert) {
            // Notify on assessment creation
            \app\components\NotificationService::notifyAssessmentCreated($this);
        } else {
            // Notify on assessment update if scores changed
            if (isset($changedAttributes['total_score']) || isset($changedAttributes['overall_level'])) {
                \app\components\NotificationService::notifyGradesComplete($this);
                
                // Notify reviewers if assessment is complete
                if ($this->total_score > 0 && count($this->grades) >= 10) {
                    \app\components\NotificationService::notifyReviewRequired($this);
                    \app\components\NotificationService::notifyTpOffice($this);
                    \app\components\NotificationService::notifyFeedbackReady($this);
                }
            }
        }
    }

}
