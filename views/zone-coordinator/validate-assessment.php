<?php
use yii\helpers\Html;

$this->title = 'Validate Assessment - ' . $model->student_reg_no;
$this->params['breadcrumbs'][] = ['label' => 'Zone Coordinator Profile', 'url' => ['/zone-coordinator/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="validate-assessment">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="mb-0">Validate Assessment Report</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                You are about to validate and approve this assessment report. This will notify the supervisor that their submission has been reviewed and validated.
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Student:</strong> <?= Html::encode($model->student_reg_no) ?></p>
                    <p><strong>School:</strong> <?= Html::encode($model->school ? $model->school->school_name : 'N/A') ?></p>
                    <p><strong>Assessment Date:</strong> <?= Html::encode($model->assessment_date) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Examiner:</strong> <?= Html::encode($model->examinerUser ? $model->examinerUser->name : 'N/A') ?></p>
                    <p><strong>Total Score:</strong> <strong><?= $model->total_score ?? 'N/A' ?></strong>/100</p>
                    <p><strong>Overall Level:</strong> <span class="badge badge-success badge-lg"><?= $model->overall_level ?? 'N/A' ?></span></p>
                </div>
            </div>

            <hr>

            <h5>Competence Summary</h5>
            <div class="table-responsive mb-4">
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
                                <td><?= $grade->score ?>/10</td>
                                <td><span class="badge badge-primary"><?= $grade->level ?></span></td>
                                <td><?= Html::encode($grade->remarks) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <hr>

            <div class="validation-checklist">
                <h6>Validation Checklist:</h6>
                <ul>
                    <li>✓ All 12 competence areas are graded</li>
                    <li>✓ Total score is calculated correctly</li>
                    <li>✓ Overall level matches the score range</li>
                    <li>✓ Remarks are complete and meaningful</li>
                    <li>✓ All required fields are filled</li>
                </ul>
            </div>

            <hr>

            <div class="form-group">
                <?php $form = \yii\widgets\ActiveForm::begin(['method' => 'post']); ?>
                    <?= Html::submitButton('<i class="fas fa-check-circle"></i> Validate & Approve', ['class' => 'btn btn-success btn-lg']) ?>
                    <?= Html::a('Back to Review', ['review-assessment', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-secondary btn-lg']) ?>
                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
