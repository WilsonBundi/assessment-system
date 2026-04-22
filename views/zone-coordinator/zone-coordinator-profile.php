<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\components\RbacHelper;

$this->title = 'Zone Coordinator Profile';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/site/dashboard']];
$this->params['breadcrumbs'][] = 'Zone Coordinator Profile';
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

    function updateZoneCoordinatorProfile() {
        $.ajax({
            url: '" . \yii\helpers\Url::to(['zone-coordinator/get-profile-data']) . "',
            type: 'GET',
            data: { last_update: lastUpdate },
            success: function(data) {
                if (data.updated) {
                    // Update statistics silently
                    $('.stat-box h3').each(function() {
                        var label = $(this).next('p').text();
                        if (label.includes('Total Assessments')) {
                            $(this).text(data.totalAssessments);
                        } else if (label.includes('Validated')) {
                            $(this).text(data.validatedAssessments);
                        } else if (label.includes('Pending Validation')) {
                            $(this).text(data.pendingValidation);
                        } else if (label.includes('Schools')) {
                            $(this).text(data.schoolCount);
                        }
                    });

                    lastUpdate = Date.now();
                }
            },
            error: function() {
                console.log('Failed to update zone coordinator profile');
            }
        });
    }

    // Start polling
    setInterval(updateZoneCoordinatorProfile, updateInterval);

    // Initial update after 5 seconds
    setTimeout(updateZoneCoordinatorProfile, 5000);
", \yii\web\View::POS_READY);
?>

<style>
    .workflow-steps .step-box {
        padding: 20px;
        margin: 10px 0;
        border-radius: 8px;
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .workflow-steps .step-box.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .workflow-steps .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #fff;
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin: 0 auto 10px;
        border: 2px solid #dee2e6;
    }

    .workflow-steps .step-box.active .step-number {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .workflow-steps h6 {
        margin-bottom: 8px;
        font-weight: 600;
    }

    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }

    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }

    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }

    .border-left-secondary {
        border-left: 4px solid #6c757d !important;
    }
</style>

<div class="zone-coordinator-profile">
    <div class="container-fluid mt-4">
        <!-- Quick Actions -->
        <div class="actions-row">
            <a href="<?= Url::to(['/zone-coordinator/profile']) ?>" class="action-card">
                <div class="action-title">Dashboard</div>
                <div class="action-button">MY DASHBOARD</div>
            </a>
            <a href="<?= Url::to(['/assessment/index']) ?>" class="action-card">
                <div class="action-title">Assessment Review</div>
                <div class="action-button">ASSESSMENTS</div>
            </a>
        </div>

        <!-- Profile Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-info shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">
                                    <i class="fas fa-user-check text-info"></i> 
                                    <?= Html::encode($coordinator ? $coordinator->name : 'Unknown') ?>
                                </h2>
                                <p class="text-muted mb-1">
                                    <strong>Role:</strong> <?= Html::encode($role && is_object($role) ? $role->role_name : 'Zone Coordinator') ?>
                                </p>
                                <p class="text-muted mb-1">
                                    <strong>Status:</strong> 
                                    <span class="badge badge-success">
                                        <?= Html::encode($coordinator && is_object($coordinator) ? $coordinator->status : 'Active') ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?= Html::a(
                                    '<i class="fas fa-list"></i> View Assessments',
                                    ['/assessment/index'],
                                    ['class' => 'btn btn-primary me-2']
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
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-address-card"></i> Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Payroll No:</strong></td>
                                    <td><?= Html::encode($coordinator && is_object($coordinator) ? $coordinator->payroll_no ?? 'N/A' : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td><?= Html::encode($coordinator && is_object($coordinator) ? $coordinator->username : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><?= Html::encode($coordinator && is_object($coordinator) ? $coordinator->phone ?? 'N/A' : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>User ID:</strong></td>
                                    <td><?= $coordinator && is_object($coordinator) ? $coordinator->user_id : 'N/A' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Assessment Statistics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Review Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-info mb-0"><?= $totalAssessments ?></h3>
                                    <p class="text-muted mb-0">Total Assessments</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-success mb-0"><?= $validatedAssessments ?></h3>
                                    <p class="text-muted mb-0">Validated</p>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-warning mb-0"><?= $pendingValidation ?></h3>
                                    <p class="text-muted mb-0">Pending Validation</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-secondary mb-0"><?= $schoolCount ?></h3>
                                    <p class="text-muted mb-0">Schools</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Systematic Assessment Workflow -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i> Assessment Review Workflow</h5>
                    </div>
                    <div class="card-body">
                        <div class="workflow-steps mb-4">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="step-box step-1 active">
                                        <div class="step-number">1</div>
                                        <h6>Review Assessment Reports</h6>
                                        <p class="small">Examine submitted assessments for completeness and accuracy</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="step-box step-2">
                                        <div class="step-number">2</div>
                                        <h6>Edit Assessment Reports</h6>
                                        <p class="small">Make corrections and updates to assessment details</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="step-box step-3">
                                        <div class="step-number">3</div>
                                        <h6>Validate Assessment Reports</h6>
                                        <p class="small">Final approval and validation of completed assessments</p>
                                    </div>
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
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical w-100">
                            <?= Html::a('<i class="fas fa-search"></i> Review All Assessments', ['/assessment/index'], ['class' => 'btn btn-outline-info mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-list"></i> Pending Validation', ['/assessment/index', 'AssessmentSearch' => ['overall_level' => '']], ['class' => 'btn btn-outline-info mb-2 text-left']) ?>
                            <?= Html::a('<i class="fas fa-poll"></i> View Dashboard', ['/site/dashboard'], ['class' => 'btn btn-outline-info text-left']) ?>
                            <?= Html::a('<i class="fas fa-check-double"></i> Validate All Pending', ['validate-all'], ['class' => 'btn btn-success mb-0', 'data-confirm' => 'Are you sure you want to validate all pending assessments at once?', 'data-method' => 'post']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 1: Review Assessment Reports -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-primary">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-search text-primary"></i> Pending Reviews</h5>
                        <small class="text-muted">Assessments submitted and awaiting your review</small>
                    </div>
                    <div class="card-body">
                        <?php if (count($submittedAssessments) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>School</th>
                                            <th>Date Submitted</th>
                                            <th>Examiner</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submittedAssessments as $assessment): ?>
                                            <tr>
                                                <td><strong><?= Html::encode($assessment->student_reg_no) ?></strong></td>
                                                <td><?= Html::encode($assessment->school ? $assessment->school->school_name : 'N/A') ?></td>
                                                <td><?= Html::encode($assessment->assessment_date) ?></td>
                                                <td><?= Html::encode($assessment->examinerUser ? $assessment->examinerUser->name : 'N/A') ?></td>
                                                <td>
                                                    <?php if ($assessment->overall_level): ?>
                                                        <span class="badge badge-info">Complete</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">Incomplete</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= Html::a('<i class="fas fa-eye"></i> Review', ['review-assessment', 'assessment_id' => $assessment->assessment_id], ['class' => 'btn btn-primary']) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p>No assessments currently need review.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: Edit Assessment Reports -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-warning">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-edit text-warning"></i> Pending Edits</h5>
                        <small class="text-muted">Assessments that require corrections or updates</small>
                    </div>
                    <div class="card-body">
                        <?php if (count($submittedAssessments) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>School</th>
                                            <th>Date Submitted</th>
                                            <th>Examiner</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submittedAssessments as $assessment): ?>
                                            <tr>
                                                <td><strong><?= Html::encode($assessment->student_reg_no) ?></strong></td>
                                                <td><?= Html::encode($assessment->school ? $assessment->school->school_name : 'N/A') ?></td>
                                                <td><?= Html::encode($assessment->assessment_date) ?></td>
                                                <td><?= Html::encode($assessment->examinerUser ? $assessment->examinerUser->name : 'N/A') ?></td>
                                                <td>
                                                    <?php if ($assessment->overall_level): ?>
                                                        <span class="badge badge-info">Complete</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">Incomplete</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <?= Html::a('<i class="fas fa-eye"></i> Review', ['review-assessment', 'assessment_id' => $assessment->assessment_id], ['class' => 'btn btn-info me-1']) ?>
                                                        <?= Html::a('<i class="fas fa-edit"></i> Edit', ['edit-assessment', 'assessment_id' => $assessment->assessment_id], ['class' => 'btn btn-warning']) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-edit fa-2x text-muted mb-2"></i>
                                <p>No assessments currently available for editing.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: Validate Assessment Reports -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-success">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-check-circle text-success"></i> Ready for Validation</h5>
                        <small class="text-muted">Completed assessments awaiting final approval</small>
                    </div>
                    <div class="card-body">
                        <?php
                        $completeAssessments = array_filter($submittedAssessments, function($assessment) {
                            return $assessment->overall_level !== null && $assessment->validated_by === null;
                        });
                        ?>
                        <?php if (count($completeAssessments) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>School</th>
                                            <th>Date Submitted</th>
                                            <th>Examiner</th>
                                            <th>Score</th>
                                            <th>Level</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($completeAssessments as $assessment): ?>
                                            <tr>
                                                <td><strong><?= Html::encode($assessment->student_reg_no) ?></strong></td>
                                                <td><?= Html::encode($assessment->school ? $assessment->school->school_name : 'N/A') ?></td>
                                                <td><?= Html::encode($assessment->assessment_date) ?></td>
                                                <td><?= Html::encode($assessment->examinerUser ? $assessment->examinerUser->name : 'N/A') ?></td>
                                                <td><strong><?= $assessment->total_score ?>/100</strong></td>
                                                <td>
                                                    <?php
                                                    $badges = [
                                                        'BE' => '<span class="badge bg-danger">BE</span>',
                                                        'AE' => '<span class="badge bg-warning text-dark">AE</span>',
                                                        'ME' => '<span class="badge bg-info">ME</span>',
                                                        'EE' => '<span class="badge bg-success">EE</span>'
                                                    ];
                                                    echo $badges[$assessment->overall_level] ?? '<span class="badge bg-secondary">N/A</span>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <?= Html::a('<i class="fas fa-eye"></i> Review', ['review-assessment', 'assessment_id' => $assessment->assessment_id], ['class' => 'btn btn-info me-1']) ?>
                                                        <?= Html::a('<i class="fas fa-edit"></i> Edit', ['edit-assessment', 'assessment_id' => $assessment->assessment_id], ['class' => 'btn btn-warning me-1']) ?>
                                                        <?= Html::a('<i class="fas fa-check-circle"></i> Validate', ['validate-assessment', 'assessment_id' => $assessment->assessment_id], ['class' => 'btn btn-success']) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                <p>No completed assessments ready for validation yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validated Assessments Summary -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-left-secondary">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-check-double text-success"></i> ✅ Validated Assessments</h5>
                        <small class="text-muted">Assessments you have reviewed and validated</small>
                    </div>
                    <div class="card-body">
                        <?php if (count($recentlyValidated) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>School</th>
                                            <th>Date Validated</th>
                                            <th>Final Score</th>
                                            <th>Final Level</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentlyValidated as $assessment): ?>
                                            <tr>
                                                <td><strong><?= Html::encode($assessment->student_reg_no) ?></strong></td>
                                                <td><?= Html::encode($assessment->school ? $assessment->school->school_name : 'N/A') ?></td>
                                                <td><?= Html::encode($assessment->validated_at ? date('Y-m-d', strtotime($assessment->validated_at)) : 'N/A') ?></td>
                                                <td><strong><?= $assessment->total_score ?>/100</strong></td>
                                                <td>
                                                    <?php
                                                    $badges = [
                                                        'BE' => '<span class="badge bg-danger">BE</span>',
                                                        'AE' => '<span class="badge bg-warning text-dark">AE</span>',
                                                        'ME' => '<span class="badge bg-info">ME</span>',
                                                        'EE' => '<span class="badge bg-success">EE</span>'
                                                    ];
                                                    echo $badges[$assessment->overall_level] ?? '<span class="badge bg-secondary">N/A</span>';
                                                    ?>
                                                </td>
                                                <td><span class="badge badge-success">Validated</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                <p>No assessments validated yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
