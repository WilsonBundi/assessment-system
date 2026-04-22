<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $zones array */
/* @var $dataByZone array */
/* @var $supervisors array */
/* @var $unassignedStudents array */
/* @var $assignments array */

$this->title = 'Student Assignments';
$this->params['breadcrumbs'][] = $this->title;

// Create supervisors map for reassign modal
$supervisorsMap = [];
foreach ($supervisors as $supervisor) {
    $zone = $supervisor->zone ? $supervisor->zone->zone_name : 'N/A';
    $supervisorsMap[$supervisor->user_id] = $supervisor->name . ' (' . $zone . ')';
}

// Determine active tab from request
$activeTab = Yii::$app->request->get('tab', 'assign');
?>

<div class="student-assignments-container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-map me-2"></i>Assign Supervisors to Students
                    </h4>
                </div>
                
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs px-3" role="tablist" style="border-bottom: 2px solid #e9ecef;">
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'assign' ? 'active' : '' ?>" 
                           href="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/student-assignments', 'tab' => 'assign'])) ?>" 
                           role="tab">
                            <i class="fas fa-link me-2"></i>Assign
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'assigned' ? 'active' : '' ?>" 
                           href="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/student-assignments', 'tab' => 'assigned'])) ?>" 
                           role="tab">
                            <i class="fas fa-check-circle me-2"></i>Assigned (<?= count($assignments) ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'unassigned' ? 'active' : '' ?>" 
                           href="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/student-assignments', 'tab' => 'unassigned'])) ?>" 
                           role="tab">
                            <i class="fas fa-circle me-2"></i>Unassigned (<?= count($unassignedStudents) ?>)
                        </a>
                    </li>
                </ul>

                <div class="card-body">
                    <!-- ASSIGN TAB -->
                    <?php if ($activeTab === 'assign'): ?>
                        <?php if (empty($dataByZone)): ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                No data available in the system.
                            </div>
                        <?php else: ?>
                            <?php foreach ($dataByZone as $zoneId => $zoneData): ?>
                                <?php $zone = $zoneData['zone']; ?>
                                <?php $supervisorsInZone = $zoneData['supervisors']; ?>
                                <?php $unassignedInZone = $zoneData['unassignedStudents']; ?>
                                
                                <!-- Only show zone if it has supervisors or unassigned students -->
                                <?php if (!empty($supervisorsInZone) || !empty($unassignedInZone)): ?>
                                    <div class="zone-section mb-5 pb-4 border-bottom">
                                        <h5 class="zone-title mb-4">
                                            <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                            <?= Html::encode($zone->zone_name) ?>
                                        </h5>

                                        <!-- Supervisors in this Zone -->
                                        <div class="mb-4">
                                            <h6 class="text-secondary fw-bold mb-3">
                                                <i class="fas fa-users-cog me-2"></i>Supervisors (<?= count($supervisorsInZone) ?>)
                                            </h6>
                                            <?php if (empty($supervisorsInZone)): ?>
                                                <div class="alert alert-warning py-2 mb-3">
                                                    <small><i class="fas fa-exclamation-triangle me-1"></i>No supervisors in this zone</small>
                                                </div>
                                            <?php else: ?>
                                                <div class="row g-2 mb-4">
                                                    <?php foreach ($supervisorsInZone as $supervisor): ?>
                                                        <div class="col-auto">
                                                            <span class="badge bg-primary p-2" style="font-size: 0.85rem;">
                                                                <i class="fas fa-user-tie me-1"></i>
                                                                <?= Html::encode($supervisor->name) ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Assignment Form for this Zone -->
                                        <?php if (!empty($supervisorsInZone) && !empty($unassignedInZone)): ?>
                                            <form method="POST" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/assign-supervisor'])) ?>" class="assignment-form mb-4">
                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small">Select Supervisor</label>
                                                        <?php 
                                                            $zoneSupervisors = [];
                                                            foreach ($supervisorsInZone as $sup) {
                                                                $zoneSupervisors[$sup->user_id] = $sup->name;
                                                            }
                                                        ?>
                                                        <?= Html::dropDownList('supervisor_user_id', null, $zoneSupervisors, [
                                                            'class' => 'form-select form-select-sm',
                                                            'prompt' => '-- Select Supervisor --',
                                                            'required' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small">Select Student</label>
                                                        <?php 
                                                            $zoneStudents = [];
                                                            foreach ($unassignedInZone as $student) {
                                                                $zoneStudents[$student->student_reg_no] = $student->name . ' - ' . $student->student_reg_no;
                                                            }
                                                        ?>
                                                        <?= Html::dropDownList('student_reg_no', null, $zoneStudents, [
                                                            'class' => 'form-select form-select-sm',
                                                            'prompt' => '-- Select Student --',
                                                            'required' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-check me-1"></i>Assign
                                                    </button>
                                                </div>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Unassigned Students in this Zone -->
                                        <div>
                                            <h6 class="text-secondary fw-bold mb-3">
                                                <i class="fas fa-user-graduate me-2"></i>Unassigned Students (<?= count($unassignedInZone) ?>)
                                            </h6>
                                            <?php if (empty($unassignedInZone)): ?>
                                                <div class="alert alert-success py-2">
                                                    <small><i class="fas fa-check-circle me-1"></i>All students are assigned</small>
                                                </div>
                                            <?php else: ?>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="fw-bold small">Student ID</th>
                                                                <th class="fw-bold small">Name</th>
                                                                <th class="fw-bold small">School</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($unassignedInZone as $student): ?>
                                                                <tr>
                                                                    <td><strong><?= Html::encode($student->student_reg_no) ?></strong></td>
                                                                    <td><?= Html::encode($student->name) ?></td>
                                                                    <td><?= $student->school ? Html::encode($student->school->school_name) : 'N/A' ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <!-- ASSIGNED TAB -->
                    <?php elseif ($activeTab === 'assigned'): ?>
                        <?php if (empty($assignments)): ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                No students have been assigned yet.
                            </div>
                        <?php else: ?>
                            <?php foreach ($dataByZone as $zoneId => $zoneData): ?>
                                <?php $assignedInZone = $zoneData['assignedStudents']; ?>
                                <?php if (!empty($assignedInZone)): ?>
                                    <div class="zone-section mb-5 pb-4 border-bottom">
                                        <h5 class="zone-title mb-4">
                                            <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                            <?= Html::encode($zoneData['zone']->zone_name) ?>
                                        </h5>

                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="fw-bold small">Student ID</th>
                                                        <th class="fw-bold small">Name</th>
                                                        <th class="fw-bold small">Supervisor</th>
                                                        <th class="fw-bold small">School</th>
                                                        <th class="fw-bold small text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($assignedInZone as $student): ?>
                                                        <?php if (isset($assignments[$student->student_reg_no])): ?>
                                                            <?php $assign = $assignments[$student->student_reg_no]; ?>
                                                            <tr>
                                                                <td><strong><?= Html::encode($student->student_reg_no) ?></strong></td>
                                                                <td><?= Html::encode($student->name) ?></td>
                                                                <td><?= Html::encode($assign['supervisor'] ? $assign['supervisor']->name : 'Unknown') ?></td>
                                                                <td><?= $student->school ? Html::encode($student->school->school_name) : 'N/A' ?></td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-warning reassign-btn" 
                                                                            data-student="<?= Html::encode($student->student_reg_no) ?>"
                                                                            data-supervisor="<?= Html::encode($assign['supervisor']->user_id ?? '') ?>">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <form method="POST" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/unassign-student'])) ?>" 
                                                                          class="d-inline" 
                                                                          onsubmit="return confirm('Remove assignment?');">
                                                                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                                        <input type="hidden" name="student_reg_no" value="<?= Html::encode($student->student_reg_no) ?>">
                                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <!-- UNASSIGNED TAB -->
                    <?php elseif ($activeTab === 'unassigned'): ?>
                        <?php if (empty($unassignedStudents)): ?>
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                All students have been assigned.
                            </div>
                        <?php else: ?>
                            <?php foreach ($dataByZone as $zoneId => $zoneData): ?>
                                <?php $unassignedInZone = $zoneData['unassignedStudents']; ?>
                                <?php if (!empty($unassignedInZone)): ?>
                                    <div class="zone-section mb-5 pb-4 border-bottom">
                                        <h5 class="zone-title mb-4">
                                            <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                            <?= Html::encode($zoneData['zone']->zone_name) ?>
                                        </h5>

                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="fw-bold small">Student ID</th>
                                                        <th class="fw-bold small">Name</th>
                                                        <th class="fw-bold small">School</th>
                                                        <th class="fw-bold small">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($unassignedInZone as $student): ?>
                                                        <tr>
                                                            <td><strong><?= Html::encode($student->student_reg_no) ?></strong></td>
                                                            <td><?= Html::encode($student->name) ?></td>
                                                            <td><?= $student->school ? Html::encode($student->school->school_name) : 'N/A' ?></td>
                                                            <td><span class="badge bg-warning">Unassigned</span></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reassign Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>Reassign Student
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/reassign-supervisor'])) ?>">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <input type="hidden" id="reassign-student-reg-no" name="student_reg_no">
                
                <div class="modal-body">
                    <label class="form-label fw-bold mb-2">Select New Supervisor</label>
                    <?= Html::dropDownList('supervisor_user_id', null, $supervisorsMap, [
                        'class' => 'form-select',
                        'prompt' => '-- Choose a supervisor --',
                        'id' => 'reassign-supervisor-select',
                        'required' => true
                    ]) ?>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i>Reassign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .student-assignments-container .card {
        border-radius: 0.5rem;
    }

    .student-assignments-container .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 1rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .student-assignments-container .nav-tabs .nav-link:hover {
        color: #495057;
        border-bottom: 3px solid #dee2e6;
    }

    .student-assignments-container .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
        background: transparent;
    }

    .zone-title {
        color: #dc3545;
        font-size: 1.1rem;
        font-weight: 600;
        border-left: 4px solid #dc3545;
        padding-left: 0.75rem;
    }

    .zone-section {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.375rem;
        border-left: 4px solid #dc3545;
    }

    .student-assignments-container .table thead th {
        background-color: #e9ecef;
        border-bottom: 2px solid #dee2e6;
        vertical-align: middle;
    }

    .student-assignments-container .table tbody tr:hover {
        background-color: white;
    }

    .assignment-form {
        background-color: #fff3cd;
        padding: 1rem;
        border-radius: 0.375rem;
        border-left: 4px solid #ffc107;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store supervisor to zone mapping
    const supervisorZoneMap = <?= json_encode(array_combine(
        array_map(fn($s) => $s->user_id, $supervisors),
        array_map(fn($s) => $s->zone_id, $supervisors)
    )) ?>;

    const allSupervisors = <?= json_encode($supervisorsMap) ?>;

    // Handle reassign button clicks
    document.querySelectorAll('.reassign-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const studentRegNo = this.getAttribute('data-student');
            document.getElementById('reassign-student-reg-no').value = studentRegNo;
            
            // Get student's zone from their current supervisor
            const currentSupervisor = this.getAttribute('data-supervisor');
            const studentZone = supervisorZoneMap[currentSupervisor];
            
            // Filter supervisors by zone in the modal
            const reassignSelect = document.getElementById('reassign-supervisor-select');
            reassignSelect.innerHTML = '<option value="">-- Choose a supervisor --</option>';
            
            Object.entries(supervisorZoneMap).forEach(([supId, supZone]) => {
                if (supZone === studentZone) {
                    const option = document.createElement('option');
                    option.value = supId;
                    option.textContent = allSupervisors[supId];
                    reassignSelect.appendChild(option);
                }
            });
            
            var modal = new bootstrap.Modal(document.getElementById('reassignModal'));
            modal.show();
        });
    });
});
</script>
