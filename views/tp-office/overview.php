<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $studentsByZone array */
/* @var $supervisors app\models\Users[] */

$this->title = 'Overview (Assigned & Unassigned)';
$this->params['breadcrumbs'][] = ['label' => 'Student Assignments', 'url' => ['student-assignments']];
$this->params['breadcrumbs'][] = $this->title;

$supervisorOptions = ArrayHelper::map($supervisors, 'user_id', function($user) {
    return $user->name . ' (' . ($user->zone ? $user->zone->zone_name : 'No Zone') . ')';
});
?>

<div class="tp-office-overview">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Overview (Assigned & Unassigned)
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($studentsByZone)): ?>
                        <?php foreach ($studentsByZone as $zoneData): ?>
                            <?php
                                $zone = $zoneData['zone'];
                                $assignedCount = count(array_filter($zoneData['students'], function($s) { return $s['assigned']; }));
                                $unassignedCount = count(array_filter($zoneData['students'], function($s) { return !$s['assigned']; }));
                            ?>

                            <div class="zone-section mb-4">
                                <h5 class="zone-title">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?= Html::encode($zone->zone_name) ?>
                                    <span class="badge bg-success ms-2"><?= $assignedCount ?> assigned</span>
                                    <span class="badge bg-warning text-dark ms-1"><?= $unassignedCount ?> unassigned</span>
                                </h5>

                                <div class="row">
                                    <!-- Assigned Column -->
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-success mb-3">
                                            <i class="fas fa-check-circle me-1"></i>Assigned Students
                                        </h6>
                                        <?php
                                            $assignedStudents = array_filter($zoneData['students'], function($s) { return $s['assigned']; });
                                        ?>
                                        <?php if (!empty($assignedStudents)): ?>
                                            <div class="list-group">
                                                <?php foreach ($assignedStudents as $studentData): ?>
                                                    <?php
                                                        $student = $studentData['user'];
                                                        $assignment = $studentData['assignment'];
                                                    ?>
                                                    <div class="list-group-item">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong><?= Html::encode($student->student_reg_no) ?></strong>
                                                                <br>
                                                                <small class="text-muted"><?= Html::encode($student->name) ?></small>
                                                                <br>
                                                                <small class="text-primary">
                                                                    <i class="fas fa-user-tie me-1"></i>
                                                                    <?= Html::encode($assignment->supervisor->name) ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info small">
                                                <i class="fas fa-info-circle me-1"></i>No assigned students
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Unassigned Column -->
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Unassigned Students
                                        </h6>
                                        <?php
                                            $unassignedStudents = array_filter($zoneData['students'], function($s) { return !$s['assigned']; });
                                        ?>
                                        <?php if (!empty($unassignedStudents)): ?>
                                            <div class="list-group">
                                                <?php foreach ($unassignedStudents as $studentData): ?>
                                                    <?php $student = $studentData['user']; ?>
                                                    <div class="list-group-item">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong><?= Html::encode($student->student_reg_no) ?></strong>
                                                                <br>
                                                                <small class="text-muted"><?= Html::encode($student->name) ?></small>
                                                            </div>
                                                            <form method="POST" action="<?= \yii\helpers\Url::to(['tp-office/student-assignments']) ?>" class="d-inline">
                                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                                <input type="hidden" name="student_reg_no" value="<?= Html::encode($student->student_reg_no) ?>">
                                                                <div class="input-group input-group-sm">
                                                                    <?= Html::dropDownList('supervisor_user_id', null, $supervisorOptions, [
                                                                        'class' => 'form-select searchable-select',
                                                                        'data-placeholder' => 'Select supervisor'
                                                                    ]) ?>
                                                                    <button type="submit" class="btn btn-primary">Assign</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-success small">
                                                <i class="fas fa-check-circle me-1"></i>All students assigned!
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>No zones or students found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tp-office-overview .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
    }

    .tp-office-overview .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }

    .tp-office-overview .card-body {
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

    .list-group-item {
        border: 1px solid #dee2e6;
        margin-bottom: 0.5rem;
    }

    .input-group-sm .form-select {
        font-size: 0.875rem;
    }

    .input-group-sm .btn {
        font-size: 0.875rem;
    }
</style>