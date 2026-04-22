<?php

namespace app\models;

/**
 * This is the model class for table "students".

 *
 * @property int $student_id
 * @property string $student_reg_no
 * @property string $surname
 * @property string $other_name
 * @property string|null $phone_no
 * @property string|null $email
 * @property int|null $school_id
 *
 * @property School $school
 */
class Students extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'students';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone_no', 'email', 'school_id'], 'default', 'value' => null],
            [['student_reg_no', 'surname', 'other_name'], 'required'],
            [['school_id'], 'default', 'value' => null],
            [['school_id'], 'integer'],
            [['student_reg_no', 'surname', 'other_name'], 'string', 'max' => 50],
            [['phone_no'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 100],
            [['student_reg_no'], 'unique'],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::class, 'targetAttribute' => ['school_id' => 'school_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'student_id' => 'Student ID',
            'student_reg_no' => 'Student Reg No',
            'surname' => 'Surname',
            'other_name' => 'Other Name',
            'phone_no' => 'Phone No',
            'email' => 'Email',
            'school_id' => 'School ID',
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
     * Gets the student's full name.
     *
     * @return string
     */
    public function getName()
    {
        return trim($this->surname . ' ' . $this->other_name);
    }

    /**
     * Gets query for [[Zone]] through the related school.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zone::class, ['zone_id' => 'zone_id'])->via('school');
    }
}
