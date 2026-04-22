<?php
use yii\helpers\Html;

$this->title = 'Review Assessment - ' . $model->student_reg_no;
$this->params['breadcrumbs'][] = ['label' => 'Zone Coordinator Profile', 'url' => ['/zone-coordinator/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="assessment-review">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="mb-0"><?= Html::encode($model->student_reg_no) ?> - Assessment Review</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>School:</strong> <?= Html::encode($model->school ? $model->school->school_name : 'N/A') ?></p>
                            <p><strong>Date:</strong> <?= Html::encode($model->assessment_date) ?></p>
                            <p><strong>Examiner:</strong> <?= Html::encode($model->examinerUser ? $model->examinerUser->name : 'N/A') ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Score:</strong> <?= $model->total_score ?? 'N/A' ?></p>
                            <p><strong>Overall Level:</strong> <span class="badge badge-info"><?= $model->overall_level ?? 'N/A' ?></span></p>
                            <p><strong>Status:</strong> 
                                <?php if ($model->archived == 1): ?>
                                    <span class="badge badge-success">Submitted</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">In Progress</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades -->
    <div class="card mb-4">
        <div class="card-header bg-info">
            <h5 class="mb-0">Competence Grades</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Competence Area</th>
                            <th>Score</th>
                            <th>Level</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->grades as $grade): ?>
                            <tr>
                                <td><?= Html::encode($grade->competence ? $grade->competence->competence_name : 'N/A') ?></td>
                                <td><?= $grade->score ?></td>
                                <td><span class="badge" style="background-color: <?= $this->render('@app/views/assessment/_grade-color', ['level' => $grade->level]) ?>"><?= $grade->level ?></span></td>
                                <td><?= Html::encode($grade->remarks) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group">
                <?= Html::a('<i class="fas fa-edit"></i> Edit Report', ['edit-assessment', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="fas fa-check-circle"></i> Validate', ['validate-assessment', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Profile', ['/zone-coordinator/profile'], ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
</div>
