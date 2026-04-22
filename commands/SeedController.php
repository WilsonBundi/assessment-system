<?php
/**
 * Seed Console Command
 * Populates initial data for the TP Assessment System
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class SeedController extends Controller
{
    /**
     * Seed all initial data (competence areas and roles)
     */
    public function actionIndex()
    {
        $this->actionCompetenceAreas();
        $this->actionRoles();
        return ExitCode::OK;
    }

    /**
     * Seed 12 competence areas from TP E24 Assessment Template
     */
    public function actionCompetenceAreas()
    {
        $db = Yii::$app->db;
        
        // Check if competence areas already exist
        $count = $db->createCommand('SELECT COUNT(*) FROM competence_area')->queryScalar();
        if ($count > 0) {
            $this->stdout("Competence areas already exist ($count records). Skipping...\n", 3);
            return ExitCode::OK;
        }

        $competenceAreas = [
            [
                'competence_name' => 'Professional Records',
                'description' => 'Ability to maintain accurate and complete professional documentation and records'
            ],
            [
                'competence_name' => 'Lesson Planning',
                'description' => 'Ability to plan lessons that align with curriculum standards and student needs'
            ],
            [
                'competence_name' => 'Introduction',
                'description' => 'Ability to introduce lessons effectively to capture student attention and establish learning objectives'
            ],
            [
                'competence_name' => 'Content Knowledge',
                'description' => 'Mastery of subject matter and ability to present accurate, relevant content'
            ],
            [
                'competence_name' => 'Pedagogical Strategies',
                'description' => 'Use of appropriate teaching strategies and methods to facilitate student learning'
            ],
            [
                'competence_name' => 'Instructional Resources',
                'description' => 'Effective selection and utilization of teaching and learning materials'
            ],
            [
                'competence_name' => 'Assessment',
                'description' => 'Use of appropriate assessment methods to evaluate student learning'
            ],
            [
                'competence_name' => 'Classroom Management',
                'description' => 'Ability to create and maintain an orderly, productive learning environment'
            ],
            [
                'competence_name' => 'Closure',
                'description' => 'Ability to effectively conclude lessons and consolidate learning'
            ],
            [
                'competence_name' => 'Professionalism',
                'description' => 'Demonstrates ethical conduct, punctuality, and professional behavior'
            ]
        ];

        foreach ($competenceAreas as $area) {
            $db->createCommand()->insert('competence_area', $area)->execute();
        }

        $this->stdout("✓ Seeded 10 competence areas\n", 2);
        return ExitCode::OK;
    }

    /**
     * Seed 4 user roles per SRS Section 3.4
     */
    public function actionRoles()
    {
        $db = Yii::$app->db;
        
        // Check if roles already exist
        $count = $db->createCommand('SELECT COUNT(*) FROM role')->queryScalar();
        if ($count > 0) {
            $this->stdout("Roles already exist ($count records). Skipping...\n", 3);
            return ExitCode::OK;
        }

        $roles = [
            ['role_name' => 'Supervisor'],
            ['role_name' => 'Zone Coordinator'],
            ['role_name' => 'TP Office'],
            ['role_name' => 'Department Chair']
        ];

        foreach ($roles as $role) {
            $db->createCommand()->insert('role', $role)->execute();
        }

        $this->stdout("✓ Seeded 4 user roles\n", 2);
        return ExitCode::OK;
    }
}
