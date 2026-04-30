<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'School Students - ' . $school->school_name;
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/site/dashboard']];
$this->params['breadcrumbs'][] = ['label' => 'Supervisor Profile', 'url' => ['/supervisor/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="school-students">
    <div class="container-fluid mt-4">
        <!-- Back Button -->
        <div class="mb-4">
            <?= Html::a(
                '<i class="fas fa-arrow-left me-2"></i>Back to Profile',
                ['/supervisor/profile'],
                ['class' => 'btn btn-secondary']
            ) ?>
        </div>

        <!-- School Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-primary shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">
                                    <i class="fas fa-school text-primary"></i> 
                                    <?= Html::encode($school->school_name) ?>
                                </h2>
                                <p class="text-muted mb-1">
                                    <strong>Zone:</strong> <?= Html::encode($school->zone ? $school->zone->zone_name : 'Unknown') ?>
                                </p>
                                <p class="text-muted mb-0">
                                    <strong>Location:</strong> <?= Html::encode($school->location ?? 'N/A') ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="text-center p-3 bg-light rounded">
                                    <h3 class="mb-1 text-primary"><?= count($students) ?></h3>
                                    <p class="text-muted mb-0">Students Assigned</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Students at <?= Html::encode($school->school_name) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($students)): ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                No students have been assigned to you at this school yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="fw-bold">Student ID</th>
                                            <th class="fw-bold">Student Name</th>
                                            <th class="fw-bold">Phone</th>
                                            <th class="fw-bold">Email</th>
                                            <th class="fw-bold">Zone</th>
                                            <th class="fw-bold">Status</th>
                                            <th class="fw-bold">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= Html::encode($student->student_reg_no) ?></strong>
                                                </td>
                                                <td><?= Html::encode($student->getName()) ?></td>
                                                <td><?= Html::encode($student->phone_no ?: 'N/A') ?></td>
                                                <td><?= Html::encode($student->email ?: 'N/A') ?></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php 
                                                        $zone = 'N/A';
                                                        if ($student->school && $student->school->zone) {
                                                            $zone = Html::encode($student->school->zone->zone_name);
                                                        }
                                                        echo $zone;
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">Active</span>
                                                </td>
                                                <td>
                                                    <?= Html::a(
                                                        '<i class="fas fa-file-alt me-1"></i> ASSESS STUDENT',
                                                        ['/supervisor/assess-student', 'student_reg_no' => $student->student_reg_no],
                                                        ['class' => 'btn btn-sm btn-success', 'style' => 'font-weight: bold;']
                                                    ) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .border-left-primary {
        border-left: 0.25rem solid #5B9BD5 !important;
    }

    .bg-primary {
        background-color: #5B9BD5 !important;
    }

    .bg-success {
        background-color: #1cc88a !important;
    }

    .bg-info {
        background-color: #36b9cc !important;
    }

    .bg-secondary {
        background-color: #858796 !important;
    }

    .text-primary {
        color: #5B9BD5 !important;
    }

    .card-header {
        padding: 1rem;
        font-size: 1rem;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>
