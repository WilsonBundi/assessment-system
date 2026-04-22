<?php
/**
 * Seed data for 4 User Roles as per SRS Section 3.4
 * These roles define access levels in the TP Assessment System
 */

use yii\db\Migration;

class RolesSeed extends Migration
{
    public function up()
    {
        $roles = [
            ['role_name' => 'Supervisor'],
            ['role_name' => 'Zone Coordinator'],
            ['role_name' => 'TP Office'],
            ['role_name' => 'Department Chair']
        ];

        foreach ($roles as $role) {
            $this->insert('role', $role);
        }
    }

    public function down()
    {
        $this->delete('role');
    }
}
