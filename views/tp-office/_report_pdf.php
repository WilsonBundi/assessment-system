<?php
/* @var $assessment app\models\Assessment */
use yii\helpers\Html;
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
.table th { background-color: #f8f9fa; font-weight: bold; }
.badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
.bg-danger { background-color: #dc3545; color: white; }
.bg-warning { background-color: #ffc107; color: #212529; }
.bg-info { background-color: #0dcaf0; color: white; }
.bg-success { background-color: #198754; color: white; }
h1, h2, h3 { color: #2E75B6; margin-top: 30px; margin-bottom: 15px; }
</style>

<div class="assessment-report">
    <h1 style="text-align: center; color: #2E75B6; margin-bottom: 30px;">TP Assessment Report</h1>

    <h2>Assessment Information</h2>
    <table class="table">
        <tbody>
            <tr>
                <th>Assessment ID</th>
                <td><?= Html::encode($assessment->assessment_id) ?></td>
            </tr>
            <tr>
                <th>Student Registration No</th>
                <td><?= Html::encode($assessment->student_reg_no) ?></td>
            </tr>
            <tr>
                <th>School</th>
                <td><?= Html::encode($assessment->school ? $assessment->school->school_name : 'N/A') ?></td>
            </tr>
            <tr>
                <th>Assessment Date</th>
                <td><?= Html::encode($assessment->assessment_date) ?></td>
            </tr>
            <tr>
                <th>Examiner</th>
                <td><?= Html::encode($assessment->examinerUser ? $assessment->examinerUser->name : 'N/A') ?></td>
            </tr>
            <tr>
                <th>Start Time</th>
                <td><?= Html::encode($assessment->start_time ?? 'N/A') ?></td>
            </tr>
            <tr>
                <th>End Time</th>
                <td><?= Html::encode($assessment->end_time ?? 'N/A') ?></td>
            </tr>
            <tr>
                <th>Total Score</th>
                <td><?= Html::encode($assessment->total_score ?? 'N/A') ?>/100</td>
            </tr>
            <tr>
                <th>Overall Level</th>
                <td>
                    <?php
                    $levels = [
                        'BE' => '<span class="badge bg-danger">BE - Below Expectations</span>',
                        'AE' => '<span class="badge bg-warning">AE - Approaching Expectations</span>',
                        'ME' => '<span class="badge bg-info">ME - Meets Expectations</span>',
                        'EE' => '<span class="badge bg-success">EE - Exceeds Expectations</span>'
                    ];
                    echo $levels[$assessment->overall_level] ?? Html::encode($assessment->overall_level ?? 'N/A');
                    ?>
                </td>
            </tr>
            <tr>
                <th>Learning Area</th>
                <td><?= Html::encode($assessment->learningArea ? $assessment->learningArea->learning_area_name : 'N/A') ?></td>
            </tr>
        </tbody>
    </table>

    <h3>Competency Grades</h3>
    <?php if ($assessment->grades): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Competence Area</th>
                    <th>Level</th>
                    <th>Score</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assessment->grades as $grade): ?>
                    <tr>
                        <td><?= Html::encode($grade->competenceArea ? $grade->competenceArea->competence_name : 'N/A') ?></td>
                        <td>
                            <?php
                            $levels = [
                                'BE' => '<span class="badge bg-danger">BE - Below Expectations</span>',
                                'AE' => '<span class="badge bg-warning">AE - Approaching Expectations</span>',
                                'ME' => '<span class="badge bg-info">ME - Meets Expectations</span>',
                                'EE' => '<span class="badge bg-success">EE - Exceeds Expectations</span>'
                            ];
                            echo $levels[$grade->level] ?? Html::encode($grade->level ?? 'N/A');
                            ?>
                        </td>
                        <td><?= Html::encode($grade->score ?? 'N/A') ?>/10</td>
                        <td><?= Html::encode($grade->remarks ?? 'N/A') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No grades recorded for this assessment.</p>
    <?php endif; ?>

    <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 20px;">
        <p>This report was generated on <?= date('Y-m-d H:i:s') ?> by the TP Assessment System.</p>
    </div>
</div>