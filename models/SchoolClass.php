<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "class".
 *
 * @property int $class_id
 * @property int $school_id
 * @property string $class_name
 *
 * @property School $school
 * @property Students[] $students
 */
class SchoolClass extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'class';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['school_id', 'class_name'], 'required'],
            [['school_id'], 'default', 'value' => null],
            [['school_id'], 'integer'],
            [['class_name'], 'string', 'max' => 50],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::class, 'targetAttribute' => ['school_id' => 'school_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'class_id' => 'Class ID',
            'school_id' => 'School ID',
            'class_name' => 'Class Name',
        ];
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
     * Gets query for [[Students]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Students::class, ['class_id' => 'class_id']);
    }

}
