<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'TP Office Profile';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/site/dashboard']];
$this->params['breadcrumbs'][] = 'TP Office Profile';
?>

<div class="tp-office-profile">
    <div class="container-fluid mt-4">
        <!-- Profile Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-danger shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">
                                    <i class="fas fa-building text-danger"></i> 
                                    <?= Html::encode($tpOffice ? $tpOffice->name : 'Unknown') ?>
                                </h2>
                                <p class="text-muted mb-1">
                                    <strong>Role:</strong> <?= Html::encode($role && is_object($role) ? $role->role_name : 'TP Office') ?>
                                </p>
                                <p class="text-muted mb-1">
                                    <strong>Status:</strong> 
                                    <span class="badge badge-success">
                                        <?= Html::encode($tpOffice && is_object($tpOffice) ? $tpOffice->status : 'Active') ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?= Html::a(
                                    '<i class="fas fa-database"></i> Master Data',
                                    ['master-data'],
                                    ['class' => 'btn btn-danger me-2']
                                ) ?>
                                <?= Html::a(
                                    '<i class="fas fa-edit"></i> Edit Profile',
                                    ['edit'],
                                    ['class' => 'btn btn-secondary']
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-address-card"></i> Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Payroll No:</strong></td>
                                    <td><?= Html::encode($tpOffice && is_object($tpOffice) ? $tpOffice->payroll_no ?? 'N/A' : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td><?= Html::encode($tpOffice && is_object($tpOffice) ? $tpOffice->username : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><?= Html::encode($tpOffice && is_object($tpOffice) ? $tpOffice->phone ?? 'N/A' : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>User ID:</strong></td>
                                    <td><?= $tpOffice && is_object($tpOffice) ? $tpOffice->user_id : 'N/A' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- System Statistics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> System Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-danger mb-0"><?= $totalAssessments ?></h3>
                                    <p class="text-muted mb-0">Total Assessments</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-success mb-0"><?= $completedAssessments ?></h3>
                                    <p class="text-muted mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-warning mb-0"><?= $inProgressAssessments ?></h3>
                                    <p class="text-muted mb-0">In Progress</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-secondary mb-0"><?= $totalUsers ?></h3>
                                    <p class="text-muted mb-0">System Users</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Resources -->
        <div class="row mb-4">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-database"></i> Master Data Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="resource-box">
                                    <h6><?= $totalSchools ?></h6>
                                    <p class="small text-muted">Schools</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="resource-box">
                                    <h6><?= $totalGrades ?></h6>
                                    <p class="small text-muted">Grade Levels</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="resource-box">
                                    <h6><?= $totalCompetenceAreas ?></h6>
                                    <p class="small text-muted">Competence Areas</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="resource-box">
                                    <h6><?= $totalSupervisors ?></h6>
                                    <p class="small text-muted">Supervisors</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Responsibilities -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i> Key Responsibilities</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="responsibility-box">
                                    <h6><i class="fas fa-users text-danger"></i> User Management</h6>
                                    <p class="small text-muted">Create, manage and delete system users</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="responsibility-box">
                                    <h6><i class="fas fa-school text-danger"></i> School Management</h6>
                                    <p class="small text-muted">Manage schools and geographical zones</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="responsibility-box">
                                    <h6><i class="fas fa-cog text-danger"></i> Configuration</h6>
                                    <p class="small text-muted">Setup competence areas, grades, learning areas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical w-100">
                            <?= Html::a('<i class="fas fa-database"></i> Master Data Configuration', ['master-data'], ['class' => 'btn btn-outline-danger mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-users"></i> Manage Users', ['/users/index'], ['class' => 'btn btn-outline-danger mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-school"></i> Manage Schools', ['/school/index'], ['class' => 'btn btn-outline-danger mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-map-pin"></i> Manage Zones', ['/zone/index'], ['class' => 'btn btn-outline-danger mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-cube"></i> Manage Competence Areas', ['/competence-area/index'], ['class' => 'btn btn-outline-danger mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-file-alt"></i> View Assessment Reports', ['/tp-office/reports'], ['class' => 'btn btn-outline-danger mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-hourglass-half"></i> Pending Validation', ['/tp-office/reports', 'status' => 'pending'], ['class' => 'btn btn-outline-danger text-left']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .resource-box {
        text-align: center;
        padding: 15px;
        border-radius: 5px;
        background: #f8f9fa;
        margin-bottom: 10px;
    }

    .resource-box h6 {
        font-size: 2rem;
        font-weight: bold;
        color: #dc3545;
        margin: 0;
    }

    .responsibility-box {
        padding: 15px;
        border-left: 4px solid #dc3545;
        margin-bottom: 15px;
    }

    .responsibility-box h6 {
        margin-top: 0;
        color: #333;
    }
</style>
