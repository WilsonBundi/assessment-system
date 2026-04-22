<?php
/**
 * Seed data for 12 Competence Areas as per TP E24 Assessment Template
 * These are the standard competence areas required for TP assessment
 */

use yii\db\Migration;

class CompetenceAreasSeed extends Migration
{
    public function up()
    {
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
            $this->insert('competence_area', $area);
        }
    }

    public function down()
    {
        $this->delete('competence_area');
    }
}
