<?php
use yii\helpers\Html;
use yii\helpers\Url;

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
    .zone-card {
        background: linear-gradient(135deg, #87CEEB 0%, #4682B4 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }
    .zone-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .zone-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .zone-title {
        display: flex;
        align-items: center;
        cursor: pointer;
        flex: 1;
    }
    .zone-title a,
    .zone-title button {
        cursor: pointer;
    }
    .school-count-link,
    .student-count-link {
        cursor: pointer;
    }
    .zone-title:hover {
        opacity: 0.8;
    }
    .school-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        transition: box-shadow 0.2s ease;
    }
    .school-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .school-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        cursor: pointer;
    }
    .school-title {
        display: flex;
        align-items: center;
        flex: 1;
    }
    .school-title:hover {
        color: #007bff;
    }
    /* New improved school list styling */
    .school-list-item {
        margin-bottom: 10px;
    }
    .school-card.border.rounded {
        transition: all 0.3s ease;
        border-left: 4px solid #007bff !important;
        background: #f8f9fa;
        cursor: pointer;
    }
    .school-card.border.rounded:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-1px);
    }
    .school-info {
        flex: 1;
    }
    .school-title.fw-bold.text-primary {
        font-size: 1.1rem;
        margin-bottom: 4px;
    }
    .school-stats {
        font-size: 0.9rem;
    }
    .student-list.collapse {
        border-top: 1px solid #dee2e6;
        padding-top: 15px;
        margin-top: 15px;
    }
    .student-container {
        max-height: 300px;
        overflow-y: auto;
    }
    .student-list {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        background: white;
    }
    .student-item {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: block;
        text-decoration: none;
        color: inherit;
    }
    .student-item:hover {
        background-color: #f8f9fa;
        text-decoration: none;
        color: inherit;
    }
    .student-item:last-child {
        border-bottom: none;
    }
    .student-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    .student-name {
        font-weight: 600;
        color: #007bff;
        font-size: 1rem;
    }
    .student-reg-no {
        font-weight: 500;
        color: #6c757d;
        font-size: 0.85rem;
    }
    .student-details {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        font-size: 0.85rem;
        color: #495057;
    }
    .student-detail-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .student-detail-item i {
        width: 14px;
        color: #6c757d;
    }
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    .load-more-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .load-more-btn:hover {
        transform: translateY(-1px);
        color: white;
    }
    .collapse-content {
        max-height: 500px;
        overflow-y: auto;
    }
</style>

<?php
$studentViewUrl = Url::to(['students/view']);
$schoolViewUrl = Url::to(['school/view']);
$manageZonesUrl = Url::to(['zone-coordinator/manage-zones']);
$this->registerJs(<<<JS
    var lastUpdate = Date.now();
    var updateInterval = 30000; // 30 seconds
    var currentZonePage = {};
    var currentSchoolPage = {};
    var studentViewUrl = '$studentViewUrl';
    var schoolViewUrl = '$schoolViewUrl';
    var manageZonesUrl = '$manageZonesUrl';

    function updateZoneCoordinatorProfile() {
        $.ajax({
            url: 'index.php?r=zone-coordinator/get-profile-data',
            type: 'GET',
            data: { last_update: lastUpdate },
            success: function(data) {
                if (data.updated) {
                    $('.stat-box h3').each(function() {
                        var label = $(this).next('p').text();
                        if (label.includes('Total Assessments')) {
                            $(this).text(data.totalAssessments);
                        } else if (label.includes('Validated')) {
                            $(this).text(data.validatedAssessments);
                        } else if (label.includes('Pending Validation')) {
                            $(this).text(data.pendingValidation);
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

    $('#assigned-zone-select').on('change', function() {
        var zoneId = $(this).val();
        if (!zoneId) {
            return;
        }

        var firstSchoolId = $(this).find('option:selected').data('first-school-id');
        var url = '$manageZonesUrl?zone_id=' + encodeURIComponent(zoneId);
        if (firstSchoolId) {
            url += '&school_id=' + encodeURIComponent(firstSchoolId);
        }
        window.location.href = url;
    });

    $(document).on('click', '.school-count-link', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var zoneId = $(this).data('zone-id');
        window.location.href = '$manageZonesUrl?zone_id=' + encodeURIComponent(zoneId);
    });

    $(document).on('click', '.student-count-link', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var zoneId = $(this).data('zone-id');
        var schoolId = $(this).data('first-school-id');
        var url = '$manageZonesUrl?zone_id=' + encodeURIComponent(zoneId);
        if (schoolId) {
            url += '&school_id=' + encodeURIComponent(schoolId);
        }
        window.location.href = url;
    });


    // Handle school card click: open school view instead of zone management
    $(document).on('click', '.school-card', function(e) {
        if ($(e.target).closest('.student-list, a, button').length) {
            return;
        }
        e.stopPropagation();

        var schoolId = $(this).data('school-id');
        window.location.href = schoolViewUrl + '?school_id=' + encodeURIComponent(schoolId);
    });

    // Handle student item click so it opens student view instead of falling back to zone navigation
    $(document).on('click', '.student-item', function(e) {
        e.stopPropagation();
        var href = $(this).attr('href');
        if (href) {
            window.location.href = href;
        }
    });

    // Manage button click should also navigate to manage zones
    $(document).on('click', '.manage-zone-btn', function(e) {
        e.stopPropagation();
        var zoneId = $(this).data('zone-id');
        window.location.href = '$manageZonesUrl?zone_id=' + encodeURIComponent(zoneId);
    });

    function loadZoneSchools(zoneId, container) {
        currentZonePage[zoneId] = currentZonePage[zoneId] || 1;
        $.ajax({
            url: 'index.php?r=zone-coordinator/get-zone-schools',
            type: 'GET',
            data: { zone_id: zoneId, page: currentZonePage[zoneId] },
            beforeSend: function() {
                container.html('<div class="text-center p-3">Loading schools...</div>');
            },
            success: function(data) {
                if (data.schools && data.schools.length > 0) {
                    data.schools.forEach(function(schoolInfo) {
                        container.append(generateSchoolHtml(schoolInfo, zoneId));
                        var schoolId = schoolInfo.school.school_id;
                        var studentContainer = container.find('.school-card[data-school-id="' + schoolId + '"]').find('.student-container');
                        loadSchoolStudents(schoolId, studentContainer);
                    });
                    if (data.hasMore) {
                        container.append('<div class=\"text-center mt-2\"><button class=\"load-more-btn\" data-zone-id=\"' + zoneId + '\">Load More Schools</button></div>');
                    }
                } else {
                    container.html('<div class=\"text-muted p-2\">No schools found for this zone.</div>');
                }
            },
            error: function() {
                container.html('<div class=\"alert alert-danger p-2\">Failed to load schools.</div>');
            }
        });
    }

    function loadSchoolStudents(schoolId, container) {
        currentSchoolPage[schoolId] = currentSchoolPage[schoolId] || 1;
        $.ajax({
            url: 'index.php?r=zone-coordinator/get-school-students',
            type: 'GET',
            data: { school_id: schoolId, page: currentSchoolPage[schoolId] },
            beforeSend: function() {
                container.html('<div class="text-center p-3">Loading students...</div>');
            },
            success: function(data) {
                container.html('');
                if (data.students && data.students.length > 0) {
                    data.students.forEach(function(student) {
                        container.append(generateStudentHtml(student));
                    });
                    if (data.hasMore) {
                        container.append('<div class=\"text-center mt-2\"><button class=\"load-more-students\" data-school-id=\"' + schoolId + '\">Load More Students</button></div>');
                    }
                } else {
                    container.html('<div class=\"text-muted p-2\">No students registered</div>');
                }
            },
            error: function() {
                container.html('<div class=\"alert alert-danger p-2\">Failed to load students.</div>');
            }
        });
    }

    // Handle load more schools
    $(document).on('click', '.load-more-btn[data-zone-id]', function() {
        var zoneId = $(this).data('zone-id');
        currentZonePage[zoneId] = (currentZonePage[zoneId] || 1) + 1;
        var container = $(this).closest('.collapse-content');
        $(this).parent().remove();
        loadZoneSchools(zoneId, container);
    });

    // Handle load more students
    $(document).on('click', '.load-more-students', function() {
        var schoolId = $(this).data('school-id');
        currentSchoolPage[schoolId] = (currentSchoolPage[schoolId] || 1) + 1;
        var container = $(this).closest('.student-list');
        $(this).parent().remove();
        loadSchoolStudents(schoolId, container);
    });

    // Generate school HTML - using a list style for better visibility
    function generateSchoolHtml(schoolInfo, zoneId) {
        return '<div class=\"school-list-item mb-2\">' +
            '<div class=\"school-card border rounded p-3 bg-light\" role=\"button\" tabindex=\"0\" data-zone-id=\"' + zoneId + '\" data-school-id=\"' + schoolInfo.school.school_id + '\">' +
                '<div class=\"d-flex justify-content-between align-items-center\">' +
                    '<div class=\"school-info\">' +
                        '<div class=\"school-title fw-bold text-primary\">' +

                            escapeHtml(schoolInfo.school.school_name) +
                        '</div>' +
                        '<div class=\"school-stats text-muted small mt-1\">' +
                            schoolInfo.studentCount + ' students' +
                        '</div>' +
                    '</div>' +

                '</div>' +
                '<div class="student-list mt-3" style="max-height: 300px; overflow-y: auto;">' +
                    '<div class=\"student-container\"></div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    // Generate student HTML
    function generateStudentHtml(student) {
        var fullName = escapeHtml(student.other_name + ' ' + student.surname);
        var regNo = escapeHtml(student.student_reg_no);
        var phone = student.phone_no ? escapeHtml(student.phone_no) : 'N/A';
        var email = student.email ? escapeHtml(student.email) : 'N/A';

        var detailsHtml = '';

        if (phone !== 'N/A') {
            detailsHtml += '<span class="student-detail-item">' + phone + '</span>';
        }

        if (email !== 'N/A') {
            detailsHtml += '<span class="student-detail-item">' + email + '</span>';
        }

        var supervisorName = student.supervisorName ? escapeHtml(student.supervisorName) : 'Unassigned';
        var supervisorEmail = student.supervisorEmail ? escapeHtml(student.supervisorEmail) : null;
        var supervisorPhone = student.supervisorPhone ? escapeHtml(student.supervisorPhone) : null;
        var supervisorHtml = '<div class="student-supervisor">' +
            '<strong>Supervisor:</strong> ' + supervisorName +
        '</div>';
        if (supervisorEmail) {
            supervisorHtml += '<div class="student-supervisor-contact text-muted"><i class="fas fa-envelope me-1"></i>' + supervisorEmail + '</div>';
        }
        if (supervisorPhone) {
            supervisorHtml += '<div class="student-supervisor-contact text-muted"><i class="fas fa-phone me-1"></i>' + supervisorPhone + '</div>';
        }

        return '<a href="' + studentViewUrl + '?student_reg_no=' + encodeURIComponent(regNo) + '" class="student-item" data-student-reg-no="' + regNo + '">' +
            '<div class="student-header">' +
                '<span class="student-name">' +
                    fullName +
                '</span>' +
                '<span class="student-reg-no">Reg: ' + regNo + '</span>' +
            '</div>' +
            (detailsHtml ? '<div class="student-details">' + detailsHtml + '</div>' : '') +
            '<div class="student-supervisor-section">' + supervisorHtml + '</div>' +
        '</a>';
    }

    // Escape HTML helper
    function escapeHtml(text) {
        return text.replace(/[&<>\"']/g, function(m) {
            switch (m) {
                case '&':
                    return '&amp;';
                case '<':
                    return '&lt;';
                case '>':
                    return '&gt;';
                case '"':
                    return '&quot;';
                case "'":
                    return '&#039;';
                default:
                    return m;
            }
        });
    }

    // Start polling
    setInterval(updateZoneCoordinatorProfile, updateInterval);

    // Initial update after 5 seconds
    setTimeout(updateZoneCoordinatorProfile, 5000);
JS, \yii\web\View::POS_READY);
?>

<div class="zone-coordinator-profile">
    <div class="container-fluid mt-4">
        <!-- Quick Actions -->
        <div class="actions-row">
            <a href="<?= Url::to(['zone-coordinator/manage-zones']) ?>" class="action-card">
                <div class="action-title">Zone Management</div>
                <div class="action-button">MY ZONES</div>
            </a>
            <a href="#assessments" class="action-card">
                <div class="action-title">Assessment Review</div>
                <div class="action-button">VALIDATE ASSESSMENTS</div>
            </a>
        </div>

        <!-- Profile Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-primary shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">
                                    <?= Html::encode($coordinator->name) ?>
                                </h2>
                                <p class="text-muted mb-1">
                                    <strong>Role:</strong> <?= Html::encode($role ? $role->role_name : 'N/A') ?>
                                </p>
                                <p class="text-muted mb-1">
                                    <strong>Status:</strong>
                                    <span class="badge badge-success">
                                        <?= Html::encode($coordinator->status) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    <div class="stat-box text-center me-3">
                                        <h3 class="text-primary mb-0"><?= $totalAssessments ?></h3>
                                        <p class="text-muted small mb-0">Total Assessments</p>
                                    </div>
                                    <div class="stat-box text-center me-3">
                                        <h3 class="text-success mb-0"><?= $validatedAssessments ?></h3>
                                        <p class="text-muted small mb-0">Validated</p>
                                    </div>
                                    <div class="stat-box text-center">
                                        <h3 class="text-warning mb-0"><?= $pendingValidation ?></h3>
                                        <p class="text-muted small mb-0">Pending</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Zones Section -->
        <div id="assigned-zones" class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            My Assigned Zones
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($zoneData)): ?>
                            <div class="alert alert-warning">
                                No zones are currently assigned to you. Please contact the TP Office for zone assignment.
                            </div>
                        <?php else: ?>
                        <div class="mb-3">
                            <label for="assigned-zone-select" class="form-label fw-bold">Select Assigned Zone</label>
                            <select id="assigned-zone-select" class="form-select form-select-lg">
                                <option value="">Choose a zone...</option>
                                <?php foreach ($zoneData as $zoneInfo): ?>
                                    <?php $firstSchoolId = isset($zoneInfo['schools'][0]['school']) ? $zoneInfo['schools'][0]['school']->school_id : null; ?>
                                    <option value="<?= $zoneInfo['zone']->zone_id ?>" data-first-school-id="<?= $firstSchoolId ?>">
                                        <?= Html::encode($zoneInfo['zone']->zone_name . ' (' . $zoneInfo['schoolCount'] . ' schools, ' . $zoneInfo['totalStudents'] . ' students)') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Validation Section -->
        <div id="assessments" class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            Assessment Validation
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Pending Assessments for Validation -->
                        <?php if (!empty($submittedAssessments)): ?>
                            <div class="mb-4">
                                <h6 class="text-warning mb-3">
                                    Pending Validation (<?= count($submittedAssessments) ?>)
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Supervisor</th>
                                                <th>School</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($submittedAssessments as $assessment): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= Html::encode($assessment->student_reg_no) ?></strong>
                                                        <?php if ($assessment->student): ?>
                                                            <br><small class="text-muted"><?= Html::encode($assessment->student->other_name . ' ' . $assessment->student->surname) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($assessment->examinerUser): ?>
                                                            <?= Html::encode($assessment->examinerUser->name) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($assessment->school): ?>
                                                            <?= Html::encode($assessment->school->school_name) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= \Yii::$app->formatter->asDate($assessment->assessment_date) ?></td>
                                                    <td>
                                                        <?= Html::a('Review & Validate', ['review-assessment', 'assessment_id' => $assessment->assessment_id], [
                                                            'class' => 'btn btn-primary btn-sm'
                                                        ]) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Recently Validated -->
                        <?php if (!empty($recentlyValidated)): ?>
                            <div>
                                <h6 class="text-success mb-3">
                                    Recently Validated (<?= count($recentlyValidated) ?>)
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
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
                                                    <td>
                                                        <strong><?= Html::encode($assessment->student_reg_no) ?></strong>
                                                        <?php if ($assessment->student): ?>
                                                            <br><small class="text-muted"><?= Html::encode($assessment->student->other_name . ' ' . $assessment->student->surname) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($assessment->school): ?>
                                                            <?= Html::encode($assessment->school->school_name) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= \Yii::$app->formatter->asDate($assessment->validated_at) ?></td>
                                                    <td>
                                                        <?php
                                                        $totalScore = 0;
                                                        if ($assessment->grades) {
                                                            foreach ($assessment->grades as $grade) {
                                                                $totalScore += $grade->score ?? 0;
                                                            }
                                                        }
                                                        echo $totalScore . '/100';
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            <?= Html::encode($assessment->overall_level ?? 'N/A') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">Validated</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>