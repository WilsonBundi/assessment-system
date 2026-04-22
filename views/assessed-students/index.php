<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\StudentSupervisorAssignment[] $assignments */

$this->title = 'Assessed Students';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assessed-students-index container mt-4">
    <h2><?= Html::encode($this->title) ?></h2>
    <div class="card mt-3">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-user-check me-2"></i> Assessed Students</h5>
        </div>
        <div class="card-body">
            <?php if (empty($assignments)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    No students have been assessed yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student Registration Number</th>
                                <th>Student Name</th>
                                <th>School</th>
                                <th>Assessed By</th>
                                <th>Assessed At</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?= Html::encode($assignment->student_reg_no) ?></td>
                                <td><?= $assignment->student ? Html::encode($assignment->student->getName()) : 'N/A' ?></td>
                                <td><?= $assignment->student && $assignment->student->school ? Html::encode($assignment->student->school->school_name) : 'N/A' ?></td>
                                <td><?= $assignment->supervisor ? Html::encode($assignment->supervisor->name) : 'N/A' ?></td>
                                <td><?= Html::encode($assignment->assigned_at) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
