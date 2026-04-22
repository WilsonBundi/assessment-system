<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "school".
 *
 * @property int $school_id
 * @property string $school_code
 * @property string $school_name
 * @property int $zone_id
 *
 * @property Assessment[] $assessments
 * @property Zone $zone
 */
class School extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'school';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['school_code', 'school_name', 'zone_id'], 'required'],
            [['zone_id'], 'default', 'value' => null],
            [['zone_id'], 'integer'],
            [['school_code'], 'string', 'max' => 20],
            [['school_name'], 'string', 'max' => 100],
            [['school_code'], 'unique'],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zone::class, 'targetAttribute' => ['zone_id' => 'zone_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'school_id' => 'School ID',
            'school_code' => 'School Code',
            'school_name' => 'School Name',
            'zone_id' => 'Zone ID',
        ];
    }

    /**
     * Gets query for [[Assessments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssessments()
    {
        return $this->hasMany(Assessment::class, ['school_id' => 'school_id']);
    }

    /**
     * Gets query for [[Zone]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zone::class, ['zone_id' => 'zone_id']);
    }

}
