<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\RbacHelper;

$this->title = 'Department Chair Profile';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/site/dashboard']];
$this->params['breadcrumbs'][] = 'Department Chair Profile';
?>

<style>
    .actions-row {
        display: flex;
        gap: 15px;
        flex-wrap: nowrap;
        justify-content: space-between;
        margin-bottom: 25px;
    }
    .action-card {
        flex: 1;
        min-width: 0;
        padding: 12px 15px;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border-radius: 8px;
        color: white;
        text-align: center;
        text-decoration: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        color: white;
        text-decoration: none;
    }
    .action-title {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 8px;
        opacity: 0.9;
    }
    .action-button {
        font-size: 0.95rem;
        font-weight: 700;
    }
</style>

<?php
$this->registerJs("
    var lastUpdate = Date.now();
    var updateInterval = 30000; // 30 seconds

    function updateDepartmentChairProfile() {
        $.ajax({
            url: '" . \yii\helpers\Url::to(['department-chair/get-profile-data']) . "',
            type: 'GET',
            data: { last_update: lastUpdate },
            success: function(data) {
                if (data.updated) {
                    // Update statistics silently
                    $('.stat-box h3').each(function() {
                        var label = $(this).next('p').text();
                        if (label.includes('Total Assessments')) {
                            $(this).text(data.totalAssessments);
                        } else if (label.includes('Completed')) {
                            $(this).text(data.completedAssessments);
                        } else if (label.includes('In Progress')) {
                            $(this).text(data.inProgressAssessments);
                        }
                    });

                    lastUpdate = Date.now();
                }
            },
            error: function() {
                console.log('Failed to update department chair profile');
            }
        });
    }

    // Start polling
    setInterval(updateDepartmentChairProfile, updateInterval);

    // Initial update after 5 seconds
    setTimeout(updateDepartmentChairProfile, 5000);
", \yii\web\View::POS_READY);
?>

<div class="department-chair-profile">
    <div class="container-fluid mt-4">
        <!-- Quick Actions -->
        <div class="actions-row">
            <a href="<?= Url::to(['/department-chair/system-reports']) ?>" class="action-card">
                <div class="action-title">System Analysis</div>
                <div class="action-button">SYSTEM REPORTS</div>
            </a>
            <a href="<?= Url::to(['/department-chair/monitor-assessments']) ?>" class="action-card">
                <div class="action-title">Assessment Monitoring</div>
                <div class="action-button">MONITOR ASSESSMENTS</div>
            </a>
        </div>

        <!-- Profile Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-warning shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">
                                    <i class="fas fa-crown text-warning"></i> 
                                    <?= Html::encode($chair ? $chair->name : 'Unknown') ?>
                                </h2>
                                <p class="text-muted mb-1">
                                    <strong>Role:</strong> <?= Html::encode($role && is_object($role) ? $role->role_name : 'Department Chair') ?>
                                </p>
                                <p class="text-muted mb-1">
                                    <strong>Status:</strong> 
                                    <span class="badge badge-success">
                                        <?= Html::encode($chair && is_object($chair) ? $chair->status : 'Active') ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?= Html::a(
                                    '<i class="fas fa-chart-line"></i> System Reports',
                                    ['system-reports'],
                                    ['class' => 'btn btn-warning me-2']
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
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-address-card"></i> Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Payroll No:</strong></td>
                                    <td><?= Html::encode($chair && is_object($chair) ? $chair->payroll_no ?? 'N/A' : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td><?= Html::encode($chair && is_object($chair) ? $chair->username : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><?= Html::encode($chair && is_object($chair) ? $chair->phone ?? 'N/A' : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>User ID:</strong></td>
                                    <td><?= $chair && is_object($chair) ? $chair->user_id : 'N/A' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- System Statistics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> System Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-warning mb-0"><?= $totalAssessments ?></h3>
                                    <p class="text-muted mb-0">Total Assessments</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-success mb-0"><?= $completedAssessments ?></h3>
                                    <p class="text-muted mb-0">Completed</p>
                                    <small class="text-muted">Validated + Level Set</small>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-info mb-0"><?= $inProgressAssessments ?></h3>
                                    <p class="text-muted mb-0">In Progress</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-secondary mb-0"><?= round($avgScore ?? 0, 1) ?></h3>
                                    <p class="text-muted mb-0">Avg Score</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="text-primary"><?= $totalSchools ?></h2>
                        <p class="text-muted mb-0">Schools</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="text-success"><?= $totalSupervisors ?></h2>
                        <p class="text-muted mb-0">Supervisors</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="text-info"><?= \app\models\Users::find()->where(['role_id' => 2])->count() ?></h2>
                        <p class="text-muted mb-0">Zone Coordinators</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Responsibilities -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i> Key Responsibilities</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="responsibility-box">
                                    <h6><i class="fas fa-eye text-warning"></i> Monitor TP Assessments</h6>
                                    <p class="small text-muted">Monitor all TP assessments across the system and track progress</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="responsibility-box">
                                    <h6><i class="fas fa-chart-line text-warning"></i> View System Reports</h6>
                                    <p class="small text-muted">View comprehensive reports on system performance and trends</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical w-100">
                            <?= Html::a('<i class="fas fa-chart-line"></i> View System Reports', ['system-reports'], ['class' => 'btn btn-outline-warning mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-list"></i> Monitor All Assessments', ['monitor-assessments'], ['class' => 'btn btn-outline-warning mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-poll"></i> View Dashboard', ['/site/dashboard'], ['class' => 'btn btn-outline-warning text-left']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Assessments Overview -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Assessments</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($recentAssessments) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>School</th>
                                            <th>Date</th>
                                            <th>Examiner</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                            <th>Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentAssessments as $assessment): ?>
                                            <tr>
                                                <td><?= Html::encode($assessment->student_reg_no) ?></td>
                                                <td><?= Html::encode($assessment->school ? $assessment->school->school_name : 'N/A') ?></td>
                                                <td><?= Html::encode($assessment->assessment_date) ?></td>
                                                <td><?= Html::encode($assessment->examinerUser ? $assessment->examinerUser->name : 'N/A') ?></td>
                                                <td>
                                                    <?php if ($assessment->archived): ?>
                                                        <span class="badge badge-secondary">Submitted</span>
                                                    <?php elseif ($assessment->overall_level): ?>
                                                        <span class="badge badge-success">Complete</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">In Progress</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $assessment->total_score ?? '-' ?></td>
                                                <td><?= $assessment->overall_level ?? '-' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No recent assessments found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
