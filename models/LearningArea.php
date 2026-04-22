<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "learning_area".
 *
 * @property int $learning_area_id
 * @property string $learning_area_name
 *
 * @property Assessment[] $assessments
 * @property Strand[] $strands
 */
class LearningArea extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'learning_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['learning_area_name'], 'required'],
            [['learning_area_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'learning_area_id' => 'Learning Area ID',
            'learning_area_name' => 'Learning Area Name',
        ];
    }

    /**
     * Gets query for [[Assessments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssessments()
    {
        return $this->hasMany(Assessment::class, ['learning_area_id' => 'learning_area_id']);
    }

    /**
     * Gets query for [[Strands]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStrands()
    {
        return $this->hasMany(Strand::class, ['learning_area_id' => 'learning_area_id']);
    }

}
