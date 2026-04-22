<?php

namespace app\commands;

use app\models\Role;
use yii\console\Controller;
use yii\console\ExitCode;

class RoleSeederController extends Controller
{
    public function actionIndex()
    {
        $roles = [
            'Supervisor',
            'Zone Coordinator',
            'TP Office',
            'Department Chair'
        ];

        foreach ($roles as $roleName) {
            // Check if already exists
            $exists = Role::findOne(['role_name' => $roleName]);
            
            if ($exists) {
                $this->stdout("⏭️  Skipping '{$roleName}' (already exists)\n");
                continue;
            }

            $model = new Role();
            $model->role_name = $roleName;

            if ($model->save()) {
                $this->stdout("✅ Added role: {$roleName}\n");
            } else {
                $this->stdout("❌ Failed to add role: {$roleName}\n");
                foreach ($model->getErrors() as $errors) {
                    foreach ($errors as $error) {
                        $this->stdout("   Error: $error\n");
                    }
                }
            }
        }

        $this->stdout("\n✅ Role seeding completed!\n");
        return ExitCode::OK;
    }
}
