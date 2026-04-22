<?php

namespace app\commands;

use app\models\CompetenceArea;
use yii\console\Controller;
use yii\console\ExitCode;

class CompetenceAreaSeederController extends Controller
{
    public function actionIndex()
    {
        $competenceAreas = [
            [
                'competence_name' => 'Professional Records',
                'description' => 'Schemes: Curriculum compliance, Completeness, Coherence of Scheme of Work. Reflective Diary, all schemes, records of work, Student Assessment records. TSC code.'
            ],
            [
                'competence_name' => 'Lesson Planning',
                'description' => 'Evidence of regular planning, concurrence with schemes of work, learning outcomes/ domains covered, learner centric experiences, Time estimates, planned instructional resources. PCI, competencies.'
            ],
            [
                'competence_name' => 'Introduction',
                'description' => 'Set induction, statement of topic, inquiry questions, entering behavior, review, and link/use of learner\' experiences.'
            ],
            [
                'competence_name' => 'Content Knowledge',
                'description' => 'Knowledge of prerequisites, relationships of concepts, mastery, depth, use of analogy.'
            ],
            [
                'competence_name' => 'Pedagogical Strategies',
                'description' => 'Appropriate methods; Learner support, Lesson pace, Grouping, collaboration, discussion, Illustration, learner involvement. Inclusion. pitch, treatment and sequencing, reinforcement.'
            ],
            [
                'competence_name' => 'Instructional Resources',
                'description' => 'Display board, charts, class-text, pictures, maps, handouts, realia, ICT integration.'
            ],
            [
                'competence_name' => 'Assessment',
                'description' => 'Interaction with students, question frequency, question variety, checking and marking classwork, class exercise, portfolios.'
            ],
            [
                'competence_name' => 'Classroom Management',
                'description' => 'Control, discipline, organization, knowledge of students. leadership, class arrangement, order. eye contact, smoothness, with-it-ness.'
            ],
            [
                'competence_name' => 'Closure',
                'description' => 'Review, evaluation, assignment, achievement of objectives.'
            ],
            [
                'competence_name' => 'Professionalism',
                'description' => 'Grooming, mannerism, communication, innovation, confidence.'
            ]
        ];

        foreach ($competenceAreas as $data) {
            // Check if already exists
            $exists = CompetenceArea::findOne(['competence_name' => $data['competence_name']]);
            
            if ($exists) {
                // Update existing record with new description
                $exists->description = $data['description'];
                if ($exists->save()) {
                    $this->stdout("🔄 Updated: {$data['competence_name']}\n");
                } else {
                    $this->stdout("❌ Failed to update: {$data['competence_name']}\n");
                }
                continue;
            }

            $model = new CompetenceArea();
            $model->competence_name = $data['competence_name'];
            $model->description = $data['description'];

            if ($model->save()) {
                $this->stdout("✅ Added: {$data['competence_name']}\n");
            } else {
                $this->stdout("❌ Failed to add: {$data['competence_name']}\n");
                foreach ($model->getErrors() as $errors) {
                    foreach ($errors as $error) {
                        $this->stdout("   Error: $error\n");
                    }
                }
            }
        }

        $this->stdout("\n✅ Competence area seeding completed!\n");
        return ExitCode::OK;
    }
}
