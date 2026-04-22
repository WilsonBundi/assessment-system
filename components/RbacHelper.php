<?php

namespace app\components;

use Yii;

/**
 * Role-Based Access Control helper
 */
class RbacHelper
{
    /**
     * Check if user has a specific role
     * @param string $roleName Role name to check
     * @return bool
     */
    public static function hasRole($roleName)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $user = Yii::$app->user->identity;
        if (!$user) {
            return false;
        }

        // Get role_id safely
        $roleId = null;
        if (property_exists($user, 'role_id')) {
            $roleId = $user->role_id;
        } elseif (isset($user->role_id)) {
            $roleId = $user->role_id;
        }

        return $roleId ? self::getUserRole($roleId) === $roleName : false;
    }

    /**
     * Get user's role name
     * @param int $roleId Role ID
     * @return string|null
     */
    public static function getUserRole($roleId)
    {
        if (!$roleId) {
            return null;
        }

        $role = \app\models\Role::findOne($roleId);
        return $role ? $role->role_name : null;
    }

    /**
     * Check if user is Supervisor (role_id = 1)
     * @return bool
     */
    public static function isSupervisor()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = Yii::$app->user->identity;
        return $user && $user->role_id == 1;
    }

    /**
     * Check if user is Zone Coordinator (role_id = 2)
     * @return bool
     */
    public static function isZoneCoordinator()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = Yii::$app->user->identity;
        return $user && $user->role_id == 2;
    }

    /**
     * Check if user is TP Office (role_id = 3)
     * @return bool
     */
    public static function isTpOffice()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = Yii::$app->user->identity;
        return $user && $user->role_id == 3;
    }

    /**
     * Check if user is Department Chair (role_id = 4)
     * @return bool
     */
    public static function isDepartmentChair()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = Yii::$app->user->identity;
        return $user && $user->role_id == 4;
    }

    /**
     * Check if user has admin/elevated privileges (TP Office only)
     * @return bool
     */
    public static function isAdmin()
    {
        return self::isTpOffice();
    }

    /**
     * Get current user role name for logged in user
     * @return string|null
     */
    public static function getCurrentUserRole()
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }

        $user = Yii::$app->user->identity;
        if (!$user || !isset($user->role_id)) {
            return null;
        }

        return self::getUserRole($user->role_id);
    }

    /**
     * Returns permitted actions by role
     * @param string|null $roleName
     * @return string[]
     */
    public static function getActionsForRole($roleName)
    {
        $roleActions = [
            'TP Office' => ['Users management', 'Schools management', 'Grades management', 'Learning Areas management', 'Competence Areas management', 'Strands management', 'Substrands management'],
            'Supervisor' => ['Create assessments', 'View assessments'],
            'Zone Coordinator' => ['Review assessments', 'Validate assessment reports'],
            'Department Chair' => ['View assessment reports', 'Monitor performance'],
        ];

        return $roleName && isset($roleActions[$roleName]) ? $roleActions[$roleName] : ['No role assigned'];
    }
}

