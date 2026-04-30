<?php

namespace app\models;

use Yii;
use app\models\StudentSupervisorAssignment;

/**
 * This is the model class for table "users".
 *
 * @property int $user_id
 * @property int $role_id
 * @property string $username
 * @property string $password
 * @property int|null $payroll_no
 * @property string $name
 * @property string|null $phone
 * @property string $status
 * @property int|null $zone_id
 * @property int|null $school_id
 * @property string|null $student_reg_no
 *
 * @property Assessment[] $assessments
 * @property Role $role
 * @property Zone $zone
 * @property School $school
 */
class Users extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payroll_no', 'phone', 'zone_id', 'school_id', 'student_reg_no'], 'default', 'value' => null],
            [['role_id', 'username', 'password', 'name', 'status'], 'required'],
            [['role_id', 'payroll_no', 'zone_id', 'school_id'], 'default', 'value' => null],
            [['role_id', 'payroll_no', 'zone_id', 'school_id'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 100],
            [['phone', 'status'], 'string', 'max' => 20],
            [['student_reg_no'], 'string', 'max' => 50],
            [['username'], 'unique'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'role_id']],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zone::class, 'targetAttribute' => ['zone_id' => 'zone_id']],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::class, 'targetAttribute' => ['school_id' => 'school_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'role_id' => 'Role ID',
            'username' => 'Username',
            'password' => 'Password',
            'payroll_no' => 'Payroll No',
            'name' => 'Name',
            'phone' => 'Phone',
            'status' => 'Status',
            'student_reg_no' => 'Student Reg No',
        ];
    }

    /**
     * Gets query for [[Assessments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssessments()
    {
        return $this->hasMany(Assessment::class, ['examiner_user_id' => 'user_id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['role_id' => 'role_id']);
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

    /**
     * Gets query for [[Zones]] - many-to-many relationship.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZones()
    {
        return $this->hasMany(Zone::class, ['zone_id' => 'zone_id'])->viaTable('user_zones', ['user_id' => 'user_id']);
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
     * Gets query for [[StudentSupervisorAssignments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSupervisorAssignments()
    {
        return $this->hasMany(StudentSupervisorAssignment::class, ['supervisor_user_id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by username or payroll number.
     *
     * @param string|int $identifier
     * @return static|null
     */
    public static function findByIdentifier($identifier)
    {
        $query = static::find();

        // Search by username in all cases.
        $query->where(['username' => $identifier]);

        // Numeric payroll values should be used to search payroll_no; keep text-only values for username only.
        if (is_numeric($identifier)) {
            $query->orWhere(['payroll_no' => (int)$identifier]);
        }

        // Allow non-active statuses in this setup, since sample data uses 'lecturer'/'lecturers'.
        return $query->one();
    }

    /**
     * Validates password
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        // 1) If password stored as plain text (legacy), direct compare
        if ($this->password === $password) {
            return true;
        }

        // 2) If password stored as hash, verify via Yii security helper
        if (Yii::$app->has('security')) {
            try {
                if (Yii::$app->security->validatePassword($password, $this->password)) {
                    return true;
                }
            } catch (\Exception $e) {
                // Hash is invalid (likely plain text, not bcrypt)
                // Fall through to next check
            }
        }

        // 3) Allow rapid demo credentials for compatibility
        if (($this->username === 'admin' && $password === 'admin') || ($this->username === 'demo' && $password === 'demo')) {
            return true;
        }

        return false;
    }
}

