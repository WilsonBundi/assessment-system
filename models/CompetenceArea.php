<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "competence_area".
 *
 * @property int $competence_id
 * @property string $competence_name
 * @property string|null $description
 *
 * @property Assessment[] $assessments
 * @property Grade[] $grades
 */
class CompetenceArea extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'competence_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'default', 'value' => null],
            [['competence_name'], 'required'],
            [['description'], 'string'],
            [['competence_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'competence_id' => 'Competence ID',
            'competence_name' => 'Competence Name',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[Assessments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssessments()
    {
        return $this->hasMany(Assessment::class, ['assessment_id' => 'assessment_id'])->viaTable('grade', ['competence_id' => 'competence_id']);
    }

    /**
     * Gets query for [[Grades]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGrades()
    {
        return $this->hasMany(Grade::class, ['competence_id' => 'competence_id']);
    }

}
