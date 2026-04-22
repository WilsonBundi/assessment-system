<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student_supervisor_assignment".
 *
 * @property int $assignment_id
 * @property string $student_reg_no
 * @property int $supervisor_user_id
 * @property int|null $zone_id
 * @property string|null $assigned_at
 *
 * @property Users $supervisor
 */
class StudentSupervisorAssignment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'student_supervisor_assignment';
    }

    public function rules()
    {
        return [
            [['student_reg_no', 'assigned_by', 'status'], 'required'],
            [['supervisor_user_id', 'zone_id', 'assigned_by'], 'integer'],
            [['assigned_at'], 'safe'],
            [['student_reg_no'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 20],
            [['student_reg_no'], 'unique'],
            [['supervisor_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['supervisor_user_id' => 'user_id']],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zone::class, 'targetAttribute' => ['zone_id' => 'zone_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'assignment_id' => 'Assignment ID',
            'student_reg_no' => 'Student Reg No',
            'supervisor_user_id' => 'Supervisor',
            'zone_id' => 'Zone',
            'assigned_at' => 'Assigned At',
        ];
    }

    public function getSupervisor()
    {
        return $this->hasOne(Users::class, ['user_id' => 'supervisor_user_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Students::class, ['student_reg_no' => 'student_reg_no']);
    }

    public function getZone()
    {
        return $this->hasOne(Zone::class, ['zone_id' => 'zone_id']);
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->isNewRecord) {
            if ($this->status === null) {
                $this->status = 'active';
            }
            if ($this->assigned_by === null && Yii::$app->has('user') && !Yii::$app->user->isGuest) {
                $this->assigned_by = Yii::$app->user->id;
            }
        }

        if ($this->status === null) {
            $this->status = 'active';
        }

        if ($this->assigned_by === null && Yii::$app->has('user') && !Yii::$app->user->isGuest) {
            $this->assigned_by = Yii::$app->user->id;
        }

        return true;
    }
}
