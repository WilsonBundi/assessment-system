<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Assessment */

$this->title = 'Assessment Details: ' . $model->student_reg_no;
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['reports']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="assessment-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Back to Reports', ['reports'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Download PDF', ['download-report', 'id' => $model->assessment_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Archive', ['archive', 'id' => $model->assessment_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to archive this assessment?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'assessment_id',
            'student_reg_no',
            [
                'attribute' => 'school_id',
                'value' => $model->school ? $model->school->school_name : 'N/A',
            ],
            'assessment_date',
            [
                'attribute' => 'examiner_user_id',
                'value' => $model->examinerUser ? $model->examinerUser->name : 'N/A',
            ],
            'start_time',
            'end_time',
            'total_score',
            'overall_level',
            [
                'attribute' => 'learning_area_id',
                'value' => $model->learningArea ? $model->learningArea->learning_area_name : 'N/A',
            ],
        ],
    ]) ?>

    <h3>Competency Grades</h3>
    <?php if ($model->grades): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Competence Area</th>
                    <th>Level</th>
                    <th>Score</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->grades as $grade): ?>
                    <tr>
                        <td><?= Html::encode($grade->competenceArea ? $grade->competenceArea->competence_name : 'N/A') ?></td>
                        <td>
                            <?php
                            $levels = [
                                'BE' => '<span class="badge bg-danger">BE - Below Expectations</span>',
                                'AE' => '<span class="badge bg-warning text-dark">AE - Approaching Expectations</span>',
                                'ME' => '<span class="badge bg-info">ME - Meets Expectations</span>',
                                'EE' => '<span class="badge bg-success">EE - Exceeds Expectations</span>'
                            ];
                            echo $levels[$grade->level] ?? $grade->level;
                            ?>
                        </td>
                        <td><?= Html::encode($grade->score) ?>/10</td>
                        <td><?= Html::encode($grade->remarks) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No grades recorded for this assessment.</p>
    <?php endif; ?>

    <h3>Evidence Images</h3>
    <?php
    // Assuming images are stored in web/uploads/assessment_{id}/
    $imagePath = Yii::getAlias('@web/uploads/assessment_' . $model->assessment_id . '/');
    $files = glob(Yii::getAlias('@webroot/uploads/assessment_' . $model->assessment_id . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE));
    if ($files): ?>
        <div class="row">
            <?php foreach ($files as $file): ?>
                <div class="col-md-3">
                    <img src="<?= $imagePath . basename($file) ?>" class="img-thumbnail" alt="Evidence" style="max-width: 100%; height: 200px; object-fit: cover;">
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No evidence images uploaded.</p>
    <?php endif; ?>
</div>