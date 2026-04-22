<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $studentsByZone array */
/* @var $supervisors app\models\Users[] */

$this->title = 'Reassign Students';
$this->params['breadcrumbs'][] = ['label' => 'Student Assignments', 'url' => ['student-assignments']];
$this->params['breadcrumbs'][] = $this->title;

$supervisorOptions = ArrayHelper::map($supervisors, 'user_id', function($user) {
    return $user->name . ' (' . ($user->zone ? $user->zone->zone_name : 'No Zone') . ')';
});
?>

<div class="tp-office-reassign-students">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Reassign Students
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <strong>Management Actions:</strong> Change supervisor assignments or remove assignments using the controls below.
                    </div>

                    <?php if (!empty($studentsByZone)): ?>
                        <?php foreach ($studentsByZone as $zoneData): ?>
                            <?php $zone = $zoneData['zone']; ?>
                            <?php $assignedZoneStudents = $zoneData['students']; ?>

                            <?php if (!empty($assignedZoneStudents)): ?>
                                <div class="zone-section mb-4">
                                    <h5 class="zone-title">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <?= Html::encode($zone->zone_name) ?>
                                        <span class="badge bg-secondary ms-2"><?= count($assignedZoneStudents) ?> students</span>
                                    </h5>

                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Student ID</th>
                                                    <th>Student Name</th>
                                                    <th>Current Supervisor</th>
                                                    <th>New Supervisor</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($assignedZoneStudents as $studentData): ?>
                                                    <?php
                                                        $student = $studentData['user'];
                                                        $assignment = $studentData['assignment'];
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= Html::encode($student->student_reg_no) ?></strong>
                                                        </td>
                                                        <td>
                                                            <?= Html::encode($student->name) ?>
                                                        </td>
                                                        <td>
                                                            <?= Html::encode($assignment->supervisor->name) ?>
                                                        </td>
                                                        <td>
                                                            <form method="POST" action="<?= \yii\helpers\Url::to(['tp-office/reassign-supervisor']) ?>" class="d-inline">
                                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                                <input type="hidden" name="student_reg_no" value="<?= Html::encode($student->student_reg_no) ?>">
                                                                <div class="input-group input-group-sm">
                                                                    <?= Html::dropDownList('supervisor_user_id', $assignment->supervisor_user_id, $supervisorOptions, [
                                                                        'class' => 'form-select',
                                                                        'style' => 'min-width: 200px;'
                                                                    ]) ?>
                                                                    <button type="submit" class="btn btn-warning">
                                                                        <i class="fas fa-exchange-alt"></i>
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            <form method="POST" action="<?= \yii\helpers\Url::to(['tp-office/unassign-student']) ?>" class="d-inline"
                                                                  onsubmit="return confirm('Are you sure you want to unassign this student from their supervisor?');">
                                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                                <input type="hidden" name="student_reg_no" value="<?= Html::encode($student->student_reg_no) ?>">
                                                                <button type="submit" class="btn btn-danger btn-sm">
                                                                    <i class="fas fa-user-times me-1"></i>Remove
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Assigned Students</h5>
                            <p class="text-muted">There are currently no students assigned to supervisors that can be reassigned.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tp-office-reassign-students .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
    }

    .tp-office-reassign-students .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }

    .tp-office-reassign-students .card-body {
        padding: 1.5rem;
    }

    .zone-title {
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    .zone-title .badge {
        font-size: 0.75rem;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }

    .table td {
        vertical-align: middle;
    }

    .input-group-sm .form-select {
        font-size: 0.875rem;
    }

    .input-group-sm .btn {
        font-size: 0.875rem;
    }
</style>