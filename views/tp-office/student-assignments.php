<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use Yii;

/* @var $this yii\web\View */
/* @var $zones array */
/* @var $dataByZone array */
/* @var $assignments array */
/* @var $unassignedStudents array */

$this->title = 'Student Assignments';
$this->params['breadcrumbs'][] = $this->title;

$supervisorsByZone = $supervisorsByZone ?? [];
$allUnassignedStudents = [];
foreach ($dataByZone as $zoneId => $zoneData) {
    foreach ($zoneData['unassignedStudents'] as $student) {
        $schoolLabel = $student->school ? $student->school->school_name : 'No school';
        $allUnassignedStudents[$student->student_reg_no] = $student->name . ' - ' . $student->student_reg_no . ' (' . $schoolLabel . ')';
    }
}

$activeTab = Yii::$app->request->get('tab', 'assign');
$selectedZoneId = Yii::$app->request->get('zone_id');

$totalAssigned = 0;
$totalUnassigned = 0;
foreach ($dataByZone as $zoneData) {
    $totalAssigned += count($zoneData['assignedStudents']);
    $totalUnassigned += count($zoneData['unassignedStudents']);
}
?>

<div class="student-assignments-container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-check me-2"></i>Assign Supervisors to Students
                    </h4>
                </div>
                
                <!-- Navigation Tabs -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <a class="btn btn-outline-primary btn-sm me-2" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/student-assignments', 'tab' => 'assigned'])) ?>">
                            Assigned (<?= $totalAssigned ?>)
                        </a>
                        <a class="btn btn-outline-secondary btn-sm me-2" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/student-assignments', 'tab' => 'unassigned'])) ?>">
                            Unassigned (<?= $totalUnassigned ?>)
                        </a>
                    </div>
                    <div>
                        <a class="btn btn-outline-success btn-sm" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/supervisor-zones'])) ?>">
                            <i class="fas fa-user-cog me-1"></i>Manage Supervisor Zones
                        </a>
                    </div>
                </div>

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
                            <i class="fas fa-check-circle me-2"></i>Assigned
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'unassigned' ? 'active' : '' ?>"
                           href="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/student-assignments', 'tab' => 'unassigned'])) ?>"
                           role="tab">
                            <i class="fas fa-circle me-2"></i>Unassigned
                        </a>
                    </li>
                </ul>

                <div class="card-body">
                    <?php if ($activeTab === 'assign'): ?>
                        <?php if (empty($dataByZone)): ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                No zones, supervisors, or students are available.
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <form method="GET" action="" class="row gx-3 align-items-end">
                                    <input type="hidden" name="tab" value="assign">
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold mb-2">Select Zone</label>
                                        <?= Html::dropDownList('zone_id', $selectedZoneId, ArrayHelper::map($zones, 'zone_id', 'zone_name'), [
                                            'class' => 'form-select searchable-select',
                                            'data-placeholder' => 'All Zones',
                                            'onchange' => 'this.form.submit()'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-7">
                                        <p class="text-muted mb-0">Choose one zone to show only that area and avoid scrolling long lists.</p>
                                    </div>
                                </form>
                            </div>
                            <?php foreach ($dataByZone as $zoneId => $zoneData): ?>
                                <?php $zone = $zoneData['zone']; ?>
                                <div class="zone-card mb-4" data-zone-id="<?= Html::encode($zone->zone_id) ?>">
                                    <div class="zone-header d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <h5 class="mb-1">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                <?= Html::encode($zone->zone_name) ?>
                                            </h5>
                                            <p class="text-muted mb-0">
                                                <span class="badge bg-primary text-white me-2">Supervisors: <?= count($zoneData['supervisors']) ?></span>
                                                <span class="badge bg-warning text-dark me-2">Unassigned: <?= count($zoneData['unassignedStudents']) ?></span>
                                                <span class="badge bg-success text-white">Assigned: <?= count($zoneData['assignedStudents']) ?></span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                        <h6 class="text-secondary fw-semibold">Schools</h6>
                                        <div class="schools-content">
                                            <?php $schools = []; ?>
                                            <?php foreach ($zoneData['unassignedStudents'] as $student): ?>
                                                <?php $schools[$student->school_id] = $student->school ? $student->school->school_name : 'Unknown School'; ?>
                                            <?php endforeach; ?>
                                            <?php if (empty($schools)): ?>
                                                <div class="alert alert-light py-2 mb-0">
                                                    <small>No school data for unassigned students.</small>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <?php foreach ($schools as $schoolName): ?>
                                                        <span class="badge bg-secondary"><?= Html::encode($schoolName) ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    </div>

                                    <?php if (empty($zoneData['supervisors'])): ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Assignments cannot be made until this zone has supervisors.
                                        </div>
                                    <?php elseif (empty($zoneData['unassignedStudents'])): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            All students in this zone are already assigned.
                                        </div>
                                    <?php else: ?>
                                        <form method="POST" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/assign-supervisor'])) ?>" class="row g-3 align-items-end">
                                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                                            <div class="col-md-5">
                                                <label class="form-label fw-bold mb-2">Select Supervisor</label>
                                                <?php
                                                $supervisorOptions = [];
                                                foreach ($supervisorsByZone as $zoneId => $zoneSupervisors) {
                                                    $zoneName = isset($zones[$zoneId]) ? $zones[$zoneId]->zone_name : 'Unknown Zone';
                                                    $supervisorOptions[$zoneName] = $zoneSupervisors;
                                                }
                                                ?>
                                                <?= Html::dropDownList('supervisor_user_id', null, $supervisorOptions, [
                                                    'class' => 'form-select searchable-select',
                                                    'data-placeholder' => '-- Choose supervisor --',
                                                    'required' => true
                                                ]) ?>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label fw-bold mb-2">Select Student</label>
                                                <?= Html::dropDownList('student_reg_no', null, ArrayHelper::map($zoneData['unassignedStudents'], 'student_reg_no', function($student) {
                                                    $phone = $student->phone_no ? ' | ' . $student->phone_no : '';
                                                    $email = $student->email ? ' | ' . $student->email : '';
                                                    return $student->name . ' - ' . $student->student_reg_no . ' (' . ($student->school ? $student->school->school_name : 'Unknown School') . ')' . $phone . $email;
                                                }), [
                                                    'class' => 'form-select searchable-select',
                                                    'data-placeholder' => '-- Choose student --',
                                                    'required' => true
                                                ]) ?>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-check me-1"></i>Assign
                                                </button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php elseif ($activeTab === 'assigned'): ?>
                        <?php if ($totalAssigned === 0): ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                No assigned students yet.
                            </div>
                        <?php else: ?>
                            <?php foreach ($dataByZone as $zoneId => $zoneData): ?>
                                <?php $zone = $zoneData['zone']; ?>
                                <div class="zone-card mb-4">
                                    <div class="zone-header mb-3">
                                        <h5 class="mb-1">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <?= Html::encode($zone->zone_name) ?>
                                        </h5>
                                        <p class="text-muted mb-0">Assigned students: <?= count($zoneData['assignedStudents']) ?></p>
                                    </div>

                                    <?php if (empty($zoneData['assignedStudents'])): ?>
                                        <div class="alert alert-light py-2">
                                            <i class="fas fa-info-circle me-1"></i> No assigned students in this zone.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 15%;">Student ID</th>
                                                        <th style="width: 30%;">Student Name</th>
                                                        <th style="width: 20%;">School</th>
                                                        <th style="width: 20%;">Supervisor</th>
                                                        <th style="width: 15%;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($zoneData['assignedStudents'] as $student): ?>
                                                        <?php $assignment = isset($assignments[$student->student_reg_no]) ? $assignments[$student->student_reg_no] : null; ?>
                                                        <?php $supervisor = $assignment ? $assignment['supervisor'] : null; ?>
                                                        <tr>
                                                            <td><strong><?= Html::encode($student->student_reg_no) ?></strong></td>
                                                            <td><?= Html::encode($student->name) ?></td>
                                                            <td><?= Html::encode($student->school ? $student->school->school_name : 'Unknown School') ?></td>
                                                            <td><?= Html::encode($supervisor ? $supervisor->name : 'Unknown') ?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-warning reassign-btn" 
                                                                        data-student="<?= Html::encode($student->student_reg_no) ?>" 
                                                                        data-zone="<?= Html::encode($zone->zone_id) ?>" 
                                                                        data-supervisor="<?= Html::encode($supervisor ? $supervisor->user_id : '') ?>">
                                                                    <i class="fas fa-edit me-1"></i>Reassign
                                                                </button>
                                                                <form method="POST" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/unassign-student'])) ?>" class="d-inline ms-1" onsubmit="return confirm('Remove assignment?');">
                                                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                                    <input type="hidden" name="student_reg_no" value="<?= Html::encode($student->student_reg_no) ?>">
                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                        <i class="fas fa-trash me-1"></i>Remove
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php elseif ($activeTab === 'unassigned'): ?>
                        <?php if ($totalUnassigned === 0): ?>
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                All students have been assigned.
                            </div>
                        <?php else: ?>
                            <?php foreach ($dataByZone as $zoneId => $zoneData): ?>
                                <?php $zone = $zoneData['zone']; ?>
                                <div class="zone-card mb-4">
                                    <div class="zone-header mb-3">
                                        <h5 class="mb-1">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <?= Html::encode($zone->zone_name) ?>
                                        </h5>
                                        <p class="text-muted mb-0">Unassigned students: <?= count($zoneData['unassignedStudents']) ?></p>
                                    </div>

                                    <?php if (empty($zoneData['unassignedStudents'])): ?>
                                        <div class="alert alert-light py-2">
                                            <i class="fas fa-check me-1"></i> No unassigned students in this zone.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 15%;">Student ID</th>
                                                        <th style="width: 30%;">Student Name</th>
                                                        <th style="width: 25%;">School</th>
                                                        <th style="width: 30%;">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($zoneData['unassignedStudents'] as $student): ?>
                                                        <tr>
                                                            <td><strong><?= Html::encode($student->student_reg_no) ?></strong></td>
                                                            <td><?= Html::encode($student->name) ?></td>
                                                            <td><?= Html::encode($student->school ? $student->school->school_name : 'Unknown School') ?></td>
                                                            <td><span class="badge bg-warning">Unassigned</span></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
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
                    <?= Html::dropDownList('supervisor_user_id', null, [], [
                        'class' => 'form-select form-select-lg searchable-select',
                        'data-placeholder' => '-- Choose a supervisor --',
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

    .student-assignments-container .zone-card {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 1.25rem;
    }

    .student-assignments-container .zone-header h5 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .student-assignments-container .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        vertical-align: middle;
    }

    .student-assignments-container .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .student-assignments-container .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .assignment-form .form-label {
        color: #495057;
        font-size: 1rem;
    }

    .assignment-form .form-select {
        min-height: 48px;
    }

    .btn-lg {
        padding: 0.75rem 2rem;
        font-size: 1rem;
        font-weight: 500;
    }
</style>

<!-- Supervisors Modal -->
<div class="modal fade" id="supervisorsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supervisors for <span id="modalZoneName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalSupervisorsContent">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supervisorsByZone = <?= json_encode($supervisorsByZone, JSON_UNESCAPED_UNICODE) ?>;
    const reassignSelect = document.getElementById('reassign-supervisor-select');

    // Supervisors modal content
    const supervisorsContent = {};
    const zoneNames = <?= json_encode(array_column($zones, 'zone_name', 'zone_id'), JSON_UNESCAPED_UNICODE) ?>;

    <?php foreach ($dataByZone as $zoneId => $zoneData): ?>
    supervisorsContent['<?= $zoneId ?>'] = `
        <div class="d-flex flex-wrap gap-2">
            <?php
            usort($zoneData['supervisors'], function($a, $b) {
                return strcmp($a->name, $b->name);
            });
            foreach ($zoneData['supervisors'] as $supervisor): ?>
            <span class="badge bg-info text-dark">
                <i class="fas fa-user-tie me-1"></i>
                <?= Html::encode($supervisor->name) ?>
            </span>
            <?php endforeach; ?>
        </div>
    `;
    <?php endforeach; ?>

    window.showSupervisorsModal = function(zoneId) {
        document.getElementById('modalZoneName').textContent = zoneNames[zoneId] || 'Unknown Zone';
        document.getElementById('modalSupervisorsContent').innerHTML = supervisorsContent[zoneId] || 'No supervisors available.';
        new bootstrap.Modal(document.getElementById('supervisorsModal')).show();
    };

    document.querySelectorAll('.reassign-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const studentRegNo = this.getAttribute('data-student');
            const zoneId = this.getAttribute('data-zone');
            const currentSupervisorId = this.getAttribute('data-supervisor');

            document.getElementById('reassign-student-reg-no').value = studentRegNo;
            reassignSelect.innerHTML = '<option value="">-- Choose a supervisor --</option>';

            if (supervisorsByZone[zoneId]) {
                Object.entries(supervisorsByZone[zoneId]).forEach(function([id, label]) {
                    const option = document.createElement('option');
                    option.value = id;
                    option.textContent = label;
                    if (id === currentSupervisorId) {
                        option.selected = true;
                    }
                    reassignSelect.appendChild(option);
                });
            }

            new bootstrap.Modal(document.getElementById('reassignModal')).show();
        });
    });

    // Real-time updates for student assignments
    let lastUpdate = 0;
    let updateInterval;

    function updateAssignments() {
        fetch('<?= Yii::$app->urlManager->createUrl(["tp-office/get-assignments-data"]) ?>?last_update=' + lastUpdate, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.updated) {
                // Update statistics in navigation
                const assignedLink = document.querySelector('a[href*="tab=assigned"]');
                const unassignedLink = document.querySelector('a[href*="tab=unassigned"]');
                
                if (assignedLink) {
                    assignedLink.innerHTML = '<i class="fas fa-check-circle me-2"></i>Assigned (' + data.stats.totalAssigned + ')';
                }
                if (unassignedLink) {
                    unassignedLink.innerHTML = '<i class="fas fa-circle me-2"></i>Unassigned (' + data.stats.totalUnassigned + ')';
                }

                // Update zone data if available
                if (data.dataByZone) {
                    updateZoneCards(data.dataByZone);
                }

                lastUpdate = data.timestamp;
            }
        })
        .catch(error => {
            console.error('Error updating assignments:', error);
        });
    }

    function updateZoneCards(dataByZone) {
        const activeTab = '<?= $activeTab ?>';
        
        dataByZone.forEach(function(zoneData) {
            const card = document.querySelector('.zone-card[data-zone-id="' + zoneData.zone.zone_id + '"]');
            if (!card) return;
            
            // Update badges
            const badges = card.querySelectorAll('.badge');
            if (badges.length >= 3) {
                badges[0].textContent = 'Supervisors: ' + zoneData.supervisors.length;
                badges[1].textContent = 'Unassigned: ' + zoneData.unassignedStudents.length;
                badges[2].textContent = 'Assigned: ' + zoneData.assignedStudents.length;
            }

            if (activeTab === 'assign') {
                updateAssignTab(card, zoneData);
            } else if (activeTab === 'assigned') {
                updateAssignedTab(card, zoneData);
            } else if (activeTab === 'unassigned') {
                updateUnassignedTab(card, zoneData);
            }
        });
    }

    function updateAssignTab(card, zoneData) {
        const supervisorsDiv = card.querySelector('.supervisors-content');
        const schoolsDiv = card.querySelector('.schools-content');
        const formSection = card.querySelector('form.row.g-3');

        // Update supervisors list
        if (zoneData.supervisors.length === 0) {
            supervisorsDiv.innerHTML = '<div class="alert alert-warning py-2 mb-0"><i class="fas fa-exclamation-triangle me-1"></i>No supervisors available for this zone.</div>';
        } else {
            let html = '<div class="d-flex flex-wrap gap-2">';
            zoneData.supervisors.forEach(function(supervisor) {
                html += '<span class="badge bg-info text-dark"><i class="fas fa-user-tie me-1"></i>' + supervisor.name + '</span>';
            });
            html += '</div>';
            supervisorsDiv.innerHTML = html;
        }

        // Update schools list
        const schools = [];
        zoneData.unassignedStudents.forEach(function(student) {
            schools.push(student.school_name);
        });
        const uniqueSchools = [...new Set(schools)];
        
        if (uniqueSchools.length === 0) {
            schoolsDiv.innerHTML = '<div class="alert alert-light py-2 mb-0"><small>No school data for unassigned students.</small></div>';
        } else {
            let html = '<div class="d-flex flex-wrap gap-2">';
            uniqueSchools.forEach(function(school) {
                html += '<span class="badge bg-secondary">' + school + '</span>';
            });
            html += '</div>';
            schoolsDiv.innerHTML = html;
        }

        // Update form or message
        if (zoneData.supervisors.length === 0) {
            formSection.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Assignments cannot be made until this zone has supervisors.</div>';
        } else if (zoneData.unassignedStudents.length === 0) {
            formSection.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>All students in this zone are already assigned.</div>';
        } else {
            // Update dropdowns
            const supervisorSelect = formSection.querySelector('select[name="supervisor_user_id"]');
            const studentSelect = formSection.querySelector('select[name="student_reg_no"]');
            
            if (supervisorSelect) {
                supervisorSelect.innerHTML = '<option value="">-- Choose supervisor --</option>';
                zoneData.supervisors.forEach(function(supervisor) {
                    const option = document.createElement('option');
                    option.value = supervisor.user_id;
                    option.textContent = supervisor.name + ' (' + supervisor.zone_name + ')';
                    supervisorSelect.appendChild(option);
                });
            }
            
            if (studentSelect) {
                studentSelect.innerHTML = '<option value="">-- Choose student --</option>';
                zoneData.unassignedStudents.forEach(function(student) {
                    const option = document.createElement('option');
                    option.value = student.student_reg_no;
                    const phone = student.phone_no ? ' | ' + student.phone_no : '';
                    const email = student.email ? ' | ' + student.email : '';
                    option.textContent = student.name + ' - ' + student.student_reg_no + ' (' + student.school_name + ')' + phone + email;
                    studentSelect.appendChild(option);
                });
            }
        }
    }

    function updateAssignedTab(card, zoneData) {
        const tableBody = card.querySelector('tbody');
        if (!tableBody) return;

        tableBody.innerHTML = '';
        zoneData.assignedStudents.forEach(function(student) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${student.student_reg_no}</strong></td>
                <td>${student.name}</td>
                <td>${student.school_name}</td>
                <td>${student.supervisor_name}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning reassign-btn" 
                            data-student="${student.student_reg_no}" 
                            data-zone="${zoneData.zone.zone_id}" 
                            data-supervisor="${student.supervisor_user_id}">
                        <i class="fas fa-edit me-1"></i>Reassign
                    </button>
                    <form method="POST" action="<?= Yii::$app->urlManager->createUrl(['tp-office/unassign-student']) ?>" class="d-inline ms-1" onsubmit="return confirm('Remove assignment?');">
                        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                        <input type="hidden" name="student_reg_no" value="${student.student_reg_no}">
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash me-1"></i>Remove
                        </button>
                    </form>
                </td>
            `;
            tableBody.appendChild(row);
        });

        // Re-bind reassign buttons
        card.querySelectorAll('.reassign-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const studentRegNo = this.getAttribute('data-student');
                const zoneId = this.getAttribute('data-zone');
                const currentSupervisorId = this.getAttribute('data-supervisor');

                document.getElementById('reassign-student-reg-no').value = studentRegNo;
                reassignSelect.innerHTML = '<option value="">-- Choose a supervisor --</option>';

                // Find supervisors for this zone
                const zoneSupervisors = supervisorsByZone[zoneId];
                if (zoneSupervisors) {
                    Object.entries(zoneSupervisors).forEach(function([id, label]) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = label;
                        if (id === currentSupervisorId) {
                            option.selected = true;
                        }
                        reassignSelect.appendChild(option);
                    });
                }

                new bootstrap.Modal(document.getElementById('reassignModal')).show();
            });
        });
    }

    function updateUnassignedTab(card, zoneData) {
        const tableBody = card.querySelector('tbody');
        if (!tableBody) return;

        tableBody.innerHTML = '';
        zoneData.unassignedStudents.forEach(function(student) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${student.student_reg_no}</strong></td>
                <td>${student.name}</td>
                <td>${student.school_name}</td>
                <td><span class="badge bg-warning">Unassigned</span></td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Start real-time updates
    updateInterval = setInterval(updateAssignments, 30000); // Update every 30 seconds

    // Initial update
    updateAssignments();
});
</script>
