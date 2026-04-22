<?php

use yii\helpers\Html;
use app\components\AuditLogger;

/** @var yii\web\View $this */
/** @var app\models\Assessment $model */

$this->title = "Audit Log - " . $model->student_reg_no;
$this->params['breadcrumbs'][] = ['label' => 'Assessments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->student_reg_no, 'url' => ['view', 'assessment_id' => $model->assessment_id]];
$this->params['breadcrumbs'][] = 'Audit Log';

// Get audit logs for this assessment
$logs = AuditLogger::getEntityLogs('assessment', $model->assessment_id, 90);
$gradeLogs = AuditLogger::getEntityLogs('grade', null, 90); // All grade logs
?>

<div class="audit-log-view">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="text-muted">
            Full audit trail of all changes to this assessment and its grades
        </p>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <?= Html::a('Back to Assessment', ['view', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <h3>Assessment Changes</h3>
    <?php if (count($logs) > 0): ?>
        <div class="timeline">
            <?php foreach ($logs as $log): ?>
                <div class="timeline-item">
                    <div class="timeline-marker">
                        <?php
                        $actionColor = [
                            'create' => 'success',
                            'update' => 'info',
                            'delete' => 'danger',
                            'submit' => 'primary',
                            'review' => 'warning',
                            'approve' => 'success'
                        ];
                        $color = $actionColor[$log['action']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $color ?>"><?= ucfirst($log['action']) ?></span>
                    </div>
                    <div class="timeline-body">
                        <h6>
                            <?= $log['user_name'] ?> <span class="text-muted">(<?= $log['user_role'] ?>)</span>
                            <small class="float-end"><?= $log['timestamp'] ?></small>
                        </h6>
                        <p><strong><?= $log['notes'] ?></strong></p>
                        
                        <?php if (count($log['changes']) > 0): ?>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($log['changes'] as $field => $change): ?>
                                        <tr>
                                            <td><?= $field ?></td>
                                            <td><code><?= $change['old'] ?></code></td>
                                            <td><code><?= $change['new'] ?></code></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                        
                        <small class="text-muted">
                            IP: <?= $log['ip_address'] ?> | User ID: <?= $log['user_id'] ?>
                        </small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No audit logs found for this assessment.
        </div>
    <?php endif; ?>

    <hr>
    <h3>Grade Changes</h3>
    <?php
    // Filter grade logs for this assessment's grades
    $assessmentGrades = $model->grades;
    $gradeIds = array_map(function($g) { return $g->grade_id; }, $assessmentGrades);
    
    $filteredGradeLogs = array_filter($gradeLogs, function($log) use ($gradeIds) {
        return in_array($log['entity_id'], $gradeIds);
    });
    ?>

    <?php if (count($filteredGradeLogs) > 0): ?>
        <div class="timeline">
            <?php foreach ($filteredGradeLogs as $log): ?>
                <div class="timeline-item">
                    <div class="timeline-marker">
                        <?php
                        $color = $actionColor[$log['action']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $color ?>"><?= ucfirst($log['action']) ?></span>
                    </div>
                    <div class="timeline-body">
                        <h6>
                            <?= $log['user_name'] ?> <span class="text-muted">(<?= $log['user_role'] ?>)</span>
                            <small class="float-end"><?= $log['timestamp'] ?></small>
                        </h6>
                        <p><strong><?= $log['notes'] ?></strong></p>
                        
                        <?php if (count($log['changes']) > 0): ?>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($log['changes'] as $field => $change): ?>
                                        <tr>
                                            <td><?= $field ?></td>
                                            <td><code><?= $change['old'] ?></code></td>
                                            <td><code><?= $change['new'] ?></code></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                        
                        <small class="text-muted">
                            Grade ID: <?= $log['entity_id'] ?> | IP: <?= $log['ip_address'] ?>
                        </small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No grade changes found for this assessment.
        </div>
    <?php endif; ?>

</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    padding-bottom: 50px;
    position: relative;
    border-left: 2px solid #ccc;
    padding-left: 30px;
    margin-left: 10px;
}

.timeline-item:last-child {
    border-left: none;
}

.timeline-marker {
    position: absolute;
    left: -42px;
    top: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-body {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.timeline-body h6 {
    margin-bottom: 10px;
}

.timeline-body .table {
    margin-bottom: 0;
}
</style>
