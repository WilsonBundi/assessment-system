<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;

$this->title = 'My Dashboard';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/site/dashboard']];
$this->params['breadcrumbs'][] = 'My Dashboard';
?>

<div class="user-profile">
    <div class="container-fluid mt-4">
        <!-- Profile Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-primary shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">
                                    <i class="fas fa-user"></i>
                                    <?= Html::encode($model->name) ?>
                                </h2>
                                <p class="text-muted mb-1">
                                    <strong>Role:</strong> <?= Html::encode($role ? $role->role_name : 'N/A') ?>
                                </p>
                                <p class="text-muted mb-1">
                                    <strong>Status:</strong>
                                    <span class="badge badge-success">
                                        <?= Html::encode($model->status) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if (\app\components\RbacHelper::isSupervisor()): ?>
                                <?= Html::a(
                                    '<i class="fas fa-plus-circle"></i> Create Assessment',
                                    ['/supervisor/select-student'],
                                    ['class' => 'btn btn-success me-2']
                                ) ?>
                                <?php endif; ?>
                                <?= Html::a(
                                    '<i class="fas fa-edit"></i> Edit Profile',
                                    ['update', 'user_id' => $model->user_id],
                                    ['class' => 'btn btn-primary']
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Assessments</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalAssessments ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Completed</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $completedAssessments ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $pendingAssessments ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Schools</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $schoolCount ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-school fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Assessments -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Assessments</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recentAssessments)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student Reg No</th>
                                            <th>School</th>
                                            <th>Assessment Date</th>
                                            <th>Overall Level</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentAssessments as $assessment): ?>
                                            <tr>
                                                <td><?= Html::encode($assessment->student_reg_no) ?></td>
                                                <td><?= Html::encode($assessment->school->school_name ?? 'N/A') ?></td>
                                                <td><?= date('M d, Y', strtotime($assessment->assessment_date)) ?></td>
                                                <td>
                                                    <?php
                                                    $level = $assessment->overall_level;
                                                    $badge = 'badge-secondary';
                                                    if ($level === 'EE') $badge = 'badge-success';
                                                    elseif ($level === 'ME') $badge = 'badge-info';
                                                    elseif ($level === 'AE') $badge = 'badge-warning';
                                                    elseif ($level === 'BE') $badge = 'badge-danger';
                                                    ?>
                                                    <span class="badge <?= $badge ?>">
                                                        <?= Html::encode($level ?? 'N/A') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= Html::a(
                                                        '<i class="fas fa-eye"></i> View',
                                                        ['/assessment/view', 'assessment_id' => $assessment->assessment_id],
                                                        ['class' => 'btn btn-sm btn-info']
                                                    ) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i> No assessments found yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>