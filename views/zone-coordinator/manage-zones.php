<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

$this->title = 'Zone Management - My Zones';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/site/dashboard']];
$this->params['breadcrumbs'][] = ['label' => 'Zone Coordinator Profile', 'url' => ['zone-coordinator/profile']];
$this->params['breadcrumbs'][] = 'Zone Management';
?>

<style>
    .zone-management-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px 0;
    }
    .zone-selector {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 25px;
        margin-bottom: 30px;
    }
    .schools-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .school-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 20px;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 2px solid transparent;
    }
    .school-card[role="button"] {
        cursor: pointer;
    }
    .school-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        border-color: #007bff;
    }
    .school-card.selected {
        border-color: #28a745;
        background: #e9fff0;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.15);
    }
    .school-card.selected .school-name {
        color: #155724;
    }
    .school-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .school-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    .student-count {
        background: #e9ecef;
        color: #495057;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .students-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 25px;
        margin-top: 20px;
    }
    .students-table-wrapper {
        width: 100%;
        overflow-x: auto;
        margin-top: 15px;
    }
    .students-table {
        width: 100%;
        min-width: 960px;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .students-table th,
    .students-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
        word-wrap: break-word;
    }
    .students-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }
    .student-supervisor {
        font-size: 0.9rem;
        color: #495057;
    }
    .student-filter-row {
        margin-bottom: 20px;
    }
    .student-filter-row .filter-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .student-filter-row .filter-group .form-control,
    .student-filter-row .filter-group .form-select {
        min-width: 240px;
        flex: 1;
    }
    @media (max-width: 992px) {
        .students-table {
            min-width: 720px;
        }
        .students-table th,
        .students-table td {
            padding: 10px;
        }
    }
    @media (max-width: 768px) {
        .students-table {
            min-width: 640px;
        }
    }
    .student-name {
        font-weight: 500;
        color: #333;
    }
    .student-reg {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .student-contact {
        font-size: 0.9rem;
        color: #495057;
    }
    .validation-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
    }
    .validation-not-validated {
        background: #fff3cd;
        color: #856404;
    }
    .validation-partially {
        background: #fff3cd;
        color: #856404;
    }
    .validation-fully {
        background: #d4edda;
        color: #155724;
    }
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    .btn-view {
        background: #007bff;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.85rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-edit {
        background: #28a745;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.85rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-view:hover, .btn-edit:hover {
        opacity: 0.8;
        color: white;
        text-decoration: none;
    }
    .btn-disabled {
        background: #6c757d;
        cursor: not-allowed;
        opacity: 0.65;
        pointer-events: none;
    }
    .loading {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
    .no-data {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
    .back-button {
        margin-bottom: 20px;
    }
</style>

<div class="zone-management-container">
    <div class="container-fluid">
        <!-- Back Button -->
        <div class="back-button">
            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Back to Profile', ['zone-coordinator/profile'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <!-- Zone Selector -->
        <div class="zone-selector">
            <h3 class="mb-4">
                <i class="fas fa-map-marked-alt text-primary me-2"></i>
                Zone Management
            </h3>

            <div class="row">
                <div class="col-md-6">
                    <label for="zone-select" class="form-label fw-bold">Select Zone:</label>
                    <select id="zone-select" class="form-select form-select-lg">
                        <option value="">Choose a zone...</option>
                        <?php foreach ($assignedZones as $zone): ?>
                            <option value="<?= $zone->zone_id ?>"><?= Html::encode($zone->zone_name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- School Selector -->
        <div id="schools-container" style="display: none;">
            <h4 class="mb-3">
                <i class="fas fa-school text-success me-2"></i>
                Select School in Zone
            </h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="school-select" class="form-label fw-bold">Select School:</label>
                    <select id="school-select" class="form-select form-select-lg">
                        <option value="">Choose a school...</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Students Container -->
        <div id="students-container" style="display: none;">
            <div class="students-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-users text-info me-2"></i>
                        Students in <span id="selected-school-name"></span>
                    </h4>
                    <div id="student-stats" class="text-muted"></div>
                </div>

                <div id="student-filter-row" class="mb-3" style="display: none;">
                    <label class="form-label fw-bold">Filter Students</label>
                    <div class="filter-group">
                        <input id="student-search" type="text" class="form-control form-control-sm" placeholder="Search by student name...">
                        <select id="supervisor-filter" class="form-select form-select-sm">
                            <option value="">All supervisors</option>
                        </select>
                        <button id="clear-filters" type="button" class="btn btn-secondary btn-sm">Clear filters</button>
                    </div>
                </div>
                <div id="students-content">
                    <!-- Students table will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$zoneSchoolData = [];
foreach ($assignedZones as $zone) {
    $zoneZoneSchools = [];
    foreach ($schools[$zone->zone_id] ?? [] as $school) {
        $zoneZoneSchools[] = [
            'school_id' => $school->school_id,
            'school_name' => $school->school_name,
            'student_count' => \app\models\Students::find()->where(['school_id' => $school->school_id])->count(),
        ];
    }
    $zoneSchoolData[] = [
        'zone_id' => $zone->zone_id,
        'schools' => $zoneZoneSchools,
    ];
}
$zoneSchoolJson = Json::htmlEncode($zoneSchoolData);
$zoneCoordinatorValidateUrl = Json::htmlEncode(Url::to(['zone-coordinator/validate-assessment']));
$zoneCoordinatorReviewUrl = Json::htmlEncode(Url::to(['zone-coordinator/review-assessment']));
$schoolStudentsUrl = Json::htmlEncode(Url::to(['zone-coordinator/get-school-students-with-status']));
$this->registerJs(<<<JS
    var selectedZoneId = null;
    var selectedSchoolId = null;
    var initialSchoolId = null;

    function getQueryParams() {
        var params = {};
        var queryString = window.location.search.substring(1);
        if (!queryString) {
            return params;
        }
        queryString.split('&').forEach(function(param) {
            var pair = param.split('=');
            if (pair[0]) {
                params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
            }
        });
        return params;
    }

    var queryParams = getQueryParams();
    selectedZoneId = queryParams.zone_id || null;
    initialSchoolId = queryParams.school_id || null;
    var zoneCoordinatorValidateUrl = $zoneCoordinatorValidateUrl;
    var zoneCoordinatorReviewUrl = $zoneCoordinatorReviewUrl;
    var zoneSchools = $zoneSchoolJson;

    if (selectedZoneId) {
        $('#zone-select').val(selectedZoneId);
        loadZoneSchools(selectedZoneId);
    }

    // Zone selection handler
    $('#zone-select').on('change', function() {
        selectedZoneId = $(this).val();
        selectedSchoolId = null;
        initialSchoolId = null;

        if (selectedZoneId) {
            loadZoneSchools(selectedZoneId);
        } else {
            $('#schools-container').hide();
            $('#students-container').hide();
        }
    });

    // Load schools for selected zone
    function loadZoneSchools(zoneId) {
        var zoneData = zoneSchools.find(function(zone) { return zone.zone_id == zoneId; });

        if (zoneData && zoneData.schools.length > 0) {
            var html = '<option value="">Choose a school...</option>';
            zoneData.schools.forEach(function(school) {
                html += '<option value="' + school.school_id + '">' + escapeHtml(school.school_name) + ' (' + school.student_count + ' students)' + '</option>';
            });

            $('#school-select').html(html);
            $('#schools-container').show();
            $('#students-container').hide();

            var targetSchoolId = initialSchoolId || $('#school-select option:not([value=""])').first().val();
            if (targetSchoolId) {
                $('#school-select').val(targetSchoolId);
                selectedSchoolId = targetSchoolId;
                $('#selected-school-name').text($('#school-select option:selected').text());
                loadSchoolStudents(targetSchoolId);
                initialSchoolId = null;
            }
        } else {
            $('#school-select').html('<option value="">No schools found in this zone</option>');
            $('#schools-container').show();
            $('#students-container').hide();
        }
    }

    // School selection handler
    $('#school-select').on('change', function() {
        selectedSchoolId = $(this).val();
        var schoolName = parseSchoolOptionText($('#school-select option:selected').text());

        if (!selectedSchoolId) {
            $('#students-container').hide();
            return;
        }

        $('#selected-school-name').text(schoolName);
        loadSchoolStudents(selectedSchoolId);
    });

    // Load students for selected school
    function loadSchoolStudents(schoolId) {
        $('#students-content').html('<div class=\"loading\"><i class=\"fas fa-spinner fa-spin fa-2x\"></i><br>Loading students...</div>');

        $.ajax({
            url: $schoolStudentsUrl,
            type: 'GET',
            data: { school_id: schoolId },
            success: function(data) {
                if (data.students && data.students.length > 0) {
                    var html = '<table class=\"students-table\">' +
                        '<thead>' +
                            '<tr>' +
                                '<th>Student Name</th>' +
                                '<th>Registration No.</th>' +
                                '<th>Contact Information</th>' +
                                '<th>Supervisor</th>' +
                                '<th>Assessment Status</th>' +
                                '<th>Assessment</th>' +
                            '</tr>' +
                        '</thead>' +
                        '<tbody>';

                    data.students.forEach(function(student) {
                        var fullName = escapeHtml(student.other_name + ' ' + student.surname);
                        var regNo = escapeHtml(student.student_reg_no);
                        var phone = student.phone_no ? escapeHtml(student.phone_no) : 'N/A';
                        var email = student.email ? escapeHtml(student.email) : 'N/A';

                        var contactInfo = '';
                        if (phone !== 'N/A') contactInfo += '<div><i class=\"fas fa-phone text-muted me-1\"></i>' + phone + '</div>';
                        if (email !== 'N/A') contactInfo += '<div><i class=\"fas fa-envelope text-muted me-1\"></i>' + email + '</div>';
                        if (!contactInfo) contactInfo = '<span class=\"text-muted\">No contact info</span>';

                        var validationClass = 'validation-not-validated';
                        if (student.validationStatus === 'Fully Validated') {
                            validationClass = 'validation-fully';
                        } else if (student.validationStatus === 'Partially Validated') {
                            validationClass = 'validation-partially';
                        }

                        var assessmentInfo = student.hasAssessments ?
                            student.validatedAssessments + '/' + student.totalAssessments + ' validated' :
                            'No assessments';

                        var supervisorName = student.supervisorName ? escapeHtml(student.supervisorName) : 'Unassigned';
                        var supervisorEmail = student.supervisorEmail ? escapeHtml(student.supervisorEmail) : null;
                        var supervisorPhone = student.supervisorPhone ? escapeHtml(student.supervisorPhone) : null;
                        var supervisorHtml = '<div class="student-supervisor-name">' + supervisorName + '</div>';
                        if (supervisorEmail) {
                            supervisorHtml += '<div class="student-supervisor-contact text-muted"><i class="fas fa-envelope me-1"></i>' + supervisorEmail + '</div>';
                        }
                        if (supervisorPhone) {
                            supervisorHtml += '<div class="student-supervisor-contact text-muted"><i class="fas fa-phone me-1"></i>' + supervisorPhone + '</div>';
                        }
                        var viewUrl = '#';
                        var actionText = 'No assessment';
                        var actionClass = 'btn-view btn-disabled';
                        if (student.assessmentId) {
                            var isValidate = student.assessmentAction === 'validate';
                            viewUrl = (isValidate ? zoneCoordinatorValidateUrl : zoneCoordinatorReviewUrl)
                                + '?assessment_id=' + encodeURIComponent(student.assessmentId);
                            actionText = isValidate ? 'Validate' : 'Review';
                            actionClass = 'btn-view';
                        }

                        html += '<tr data-supervisor="' + escapeHtml(supervisorName) + '">' +
                            '<td><div class="student-name">' + fullName + '</div></td>' +
                            '<td><div class="student-reg">' + regNo + '</div></td>' +
                            '<td><div class="student-contact">' + contactInfo + '</div></td>' +
                            '<td><div class="student-supervisor">' + supervisorHtml + '</div></td>' +
                            '<td><span class=\"validation-badge ' + validationClass + '\">' + student.validationStatus + '</span><br><small class=\"text-muted\">' + assessmentInfo + '</small></td>' +
                            '<td>' +
                                '<div class=\"action-buttons\">' +
                                    '<a href="' + viewUrl + '" class="' + actionClass + '" title="Open Assessment"' + (student.assessmentId ? '' : ' tabindex="-1" aria-disabled="true"') + '>' +
                                        '<i class="fas fa-eye"></i> ' + actionText +
                                    '</a>' +
                                '</div>' +
                            '</td>' +
                        '</tr>';
                    });

                    html += '</tbody></table>';

                    function populateSupervisorFilter(students) {
                        var supervisors = {};
                        students.forEach(function(student) {
                            var name = student.supervisorName ? student.supervisorName.trim() : 'Unassigned';
                            supervisors[name] = true;
                        });

                        var filterHtml = '<option value="">All supervisors</option>';
                        Object.keys(supervisors).sort().forEach(function(name) {
                            filterHtml += '<option value="' + escapeHtml(name) + '">' + escapeHtml(name) + '</option>';
                        });

                        $('#supervisor-filter').html(filterHtml);
                        $('#student-filter-row').show();
                        $('#supervisor-filter').off('change').on('change', filterStudentRows);
                        $('#student-search').off('input').on('input', filterStudentRows);
                        $('#clear-filters').off('click').on('click', function() {
                            $('#student-search').val('');
                            $('#supervisor-filter').val('');
                            filterStudentRows();
                        });
                    }

                    function filterStudentRows() {
                        var selectedSupervisor = $('#supervisor-filter').val();
                        var searchTerm = $('#student-search').val().trim().toLowerCase();

                        $('#students-content tbody tr').each(function() {
                            var rowSupervisor = $(this).data('supervisor');
                            var studentName = $(this).find('.student-name').text().toLowerCase();
                            var matchesSupervisor = !selectedSupervisor || rowSupervisor === selectedSupervisor;
                            var matchesSearch = !searchTerm || studentName.indexOf(searchTerm) !== -1;

                            if (matchesSupervisor && matchesSearch) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }

                    // Update stats
                    var totalStudents = data.students.length;
                    var validatedStudents = data.students.filter(function(s) { return s.validationStatus === 'Fully Validated'; }).length;
                    var partiallyValidated = data.students.filter(function(s) { return s.validationStatus === 'Partially Validated'; }).length;

                    $('#student-stats').html(
                        '<strong>' + totalStudents + '</strong> total students | ' +
                        '<strong>' + validatedStudents + '</strong> fully validated | ' +
                        '<strong>' + partiallyValidated + '</strong> partially validated'
                    );

                    $('#students-content').html('<div class="students-table-wrapper">' + html + '</div>');
                    populateSupervisorFilter(data.students);
                    filterStudentRows();
                } else {
                    $('#students-content').html('<div class=\"no-data\"><i class=\"fas fa-users fa-3x text-muted mb-3\"></i><br>No students found in this school.</div>');
                    $('#student-stats').html('');
                }

                $('#students-container').show();
            },
            error: function() {
                $('#students-content').html('<div class=\"alert alert-danger\">Failed to load students. Please try again.</div>');
                $('#students-container').show();
            }
        });
    }

    // Escape HTML helper
    function escapeHtml(text) {
        if (!text) return '';
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '\"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
    }
    function parseSchoolOptionText(text) {
        return text.replace(/\s*\(\d+\s+students\)$/, '');
    }
JS, \yii\web\View::POS_READY);
?>