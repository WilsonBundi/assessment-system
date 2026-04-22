<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grade".
 * 
 * Implements TP E24 Assessment grading scale:
 * BE (Below Expectations): 0-3 points
 * AE (Approaching Expectations): 4-5 points  
 * ME (Meets Expectations): 6-7 points
 * EE (Exceeds Expectations): 8-10 points
 *
 * @property int $grade_id
 * @property int $assessment_id
 * @property int $competence_id
 * @property string $level
 * @property int|null $score
 * @property string|null $remarks
 *
 * @property Assessment $assessment
 * @property CompetenceArea $competence
 */
class Grade extends \yii\db\ActiveRecord
{
    const LEVEL_BE = 'BE';
    const LEVEL_AE = 'AE';
    const LEVEL_ME = 'ME';
    const LEVEL_EE = 'EE';

    /**
     * Grading Scale per TP E24 Assessment Template
     * Maps levels to score ranges
     */
    public static function getGradingScale()
    {
        return [
            self::LEVEL_BE => ['label' => 'Below Expectations', 'min' => 0, 'max' => 3],
            self::LEVEL_AE => ['label' => 'Approaching Expectations', 'min' => 4, 'max' => 5],
            self::LEVEL_ME => ['label' => 'Meets Expectations', 'min' => 6, 'max' => 7],
            self::LEVEL_EE => ['label' => 'Exceeds Expectations', 'min' => 8, 'max' => 10],
        ];
    }

    /**
     * Get all valid levels
     */
    public static function getValidLevels()
    {
        return array_keys(self::getGradingScale());
    }

    /**
     * Get level label
     */
    public static function getLevelLabel($level)
    {
        $scale = self::getGradingScale();
        return isset($scale[$level]) ? $scale[$level]['label'] : 'Unknown';
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grade';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['score', 'remarks'], 'default', 'value' => null],
            [['assessment_id', 'competence_id', 'level'], 'required'],
            [['assessment_id', 'competence_id', 'score'], 'default', 'value' => null],
            [['assessment_id', 'competence_id', 'score'], 'integer'],
            [['score'], 'integer', 'min' => 0, 'max' => 10],
            [['remarks'], 'string'],
            [['level'], 'string', 'max' => 5],
            [['level'], 'in', 'range' => self::getValidLevels(), 'message' => 'Invalid grading level'],
            [['assessment_id', 'competence_id'], 'unique', 'targetAttribute' => ['assessment_id', 'competence_id']],
            [['assessment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Assessment::class, 'targetAttribute' => ['assessment_id' => 'assessment_id']],
            [['competence_id'], 'exist', 'skipOnError' => true, 'targetClass' => CompetenceArea::class, 'targetAttribute' => ['competence_id' => 'competence_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'grade_id' => 'Grade ID',
            'assessment_id' => 'Assessment',
            'competence_id' => 'Competence Area',
            'level' => 'Performance Level',
            'score' => 'Score (0-10)',
            'remarks' => 'Remarks',
        ];
    }

    /**
     * Gets query for [[Assessment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssessment()
    {
        return $this->hasOne(Assessment::class, ['assessment_id' => 'assessment_id']);
    }

    /**
     * Gets query for [[Competence]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompetence()
    {
        return $this->hasOne(CompetenceArea::class, ['competence_id' => 'competence_id']);
    }

    public function getCompetenceArea()
    {
        return $this->hasOne(CompetenceArea::class, ['competence_id' => 'competence_id']);
    }

    /**
     * Log audit events after save
     */
    public static function generateDefaultRemark($competenceId, $level)
    {
        $competence = CompetenceArea::findOne($competenceId);
        $name = $competence ? $competence->competence_name : 'Competence area';

        $prefix = '';
        switch ($level) {
            case self::LEVEL_EE:
                $prefix = 'Excellent performance';
                break;
            case self::LEVEL_ME:
                $prefix = 'Meets expectations';
                break;
            case self::LEVEL_AE:
                $prefix = 'Approaching expectations';
                break;
            case self::LEVEL_BE:
            default:
                $prefix = 'Needs improvement';
                break;
        }

        return $prefix . ' in ' . $name . '. Keep practicing to further improve demonstration skills.';
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty(trim($this->remarks ?? '')) && $this->competence_id && $this->level) {
                $this->remarks = self::generateDefaultRemark($this->competence_id, $this->level);
            }
            return true;
        }
        return false;
    }

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
            'grade',
            $this->grade_id,
            $changes,
            $insert ? "Grade created for competence {$this->competence_id}" : "Grade updated"
        );
    }

}
