<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $recentAssessments array */
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Student</th>
            <th>School</th>
            <th>Date</th>
            <th>Examiner</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentAssessments as $assessment): ?>
        <tr class="new-assessment" style="animation: highlight 2s ease-out;">
            <td><?= Html::encode($assessment->student_reg_no) ?></td>
            <td><?= Html::encode($assessment->school->school_name ?? 'N/A') ?></td>
            <td><?= Html::encode($assessment->assessment_date) ?></td>
            <td><?= Html::encode($assessment->examinerUser->name ?? 'N/A') ?></td>
            <td>
                <?= Html::a('View', ['view', 'id' => $assessment->assessment_id], ['class' => 'btn btn-sm btn-info']) ?>
                <?= Html::a('Download', ['download-report', 'id' => $assessment->assessment_id], ['class' => 'btn btn-sm btn-success']) ?>
                <?= Html::a('Archive', ['archive', 'id' => $assessment->assessment_id], ['class' => 'btn btn-sm btn-danger', 'data-confirm' => 'Are you sure you want to archive this assessment?']) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<style>
    @keyframes highlight {
        0% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }

    .new-assessment {
        animation: highlight 3s ease-out;
    }
</style>