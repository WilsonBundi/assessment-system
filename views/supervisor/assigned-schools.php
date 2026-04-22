<?php
use yii\helpers\Html;

$this->title = 'Assigned Schools';
$this->params['breadcrumbs'][] = ['label' => 'Supervisor Profile', 'url' => ['/supervisor/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="assigned-schools-page">
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-school"></i> Assigned Schools (<?= count($assignedSchools) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($assignedSchools)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    No schools are assigned to you yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-bold">School Name</th>
                                <th class="fw-bold">Students Assigned</th>
                                <th class="fw-bold">Zone</th>
                                <th class="fw-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignedSchools as $schoolId => $school): ?>
                                <tr>
                                    <td><strong><?= Html::encode($school->school_name) ?></strong></td>
                                    <td><span class="badge bg-primary"><?= Html::encode($schoolStudentCounts[$schoolId] ?? 0) ?> Students</span></td>
                                    <td><span class="badge bg-secondary"><?= Html::encode($school->zone ? $school->zone->zone_name : 'Zone unknown') ?></span></td>
                                    <td>
                                        <?= Html::a('<i class="fas fa-users"></i> View Students', ['supervisor/school-students', 'school_id' => $schoolId], ['class' => 'btn btn-sm btn-info', 'style' => 'font-weight: bold;']) ?>
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
