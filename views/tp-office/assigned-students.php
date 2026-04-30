<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $studentsByZone array */

$this->title = 'Assigned Students';
$this->params['breadcrumbs'][] = ['label' => 'Student Assignments', 'url' => ['student-assignments']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tp-office-assigned-students">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Assigned Students
                    </h4>
                    <a href="<?= \yii\helpers\Url::to(['tp-office/download-assigned-students-excel']) ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-download me-1"></i>Download Excel
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($studentsByZone)): ?>
                        <?php foreach ($studentsByZone as $zoneData): ?>
                            <?php $zone = $zoneData['zone']; ?>
                            <?php $assignedZoneStudents = $zoneData['students']; ?>

                            <?php if (!empty($assignedZoneStudents)): ?>
                                <h5 class="mb-3">
                                    <?= Html::encode($zone->zone_name) ?>
                                </h5>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Student ID</th>
                                                <th>Student Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Supervisor</th>
                                                <th>Assigned Date</th>
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
                                                        <?= Html::encode($student->phone_no ?: 'N/A') ?>
                                                    </td>
                                                    <td>
                                                        <?= Html::encode($student->email ?: 'N/A') ?>
                                                    </td>
                                                    <td>
                                                        <?= Html::encode($assignment->supervisor->name) ?>
                                                    </td>
                                                    <td>
                                                        <?= Html::encode(Yii::$app->formatter->asDate($assignment->assigned_at, 'medium')) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Assigned Students</h5>
                            <p class="text-muted">There are currently no students assigned to supervisors.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tp-office-assigned-students .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
    }

    .tp-office-assigned-students .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }

    .tp-office-assigned-students .card-body {
        padding: 1.5rem;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }

    .table td {
        vertical-align: middle;
    }
</style>