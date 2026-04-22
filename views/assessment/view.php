<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\components\AssessmentImageBehavior;
use app\components\RbacHelper;

/** @var yii\web\View $this */
/** @var app\models\Assessment $model */

$this->title = "Assessment - " . $model->student_reg_no;
$this->params['breadcrumbs'][] = ['label' => 'Assessments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="assessment-view">

    <div class="page-header d-flex align-items-center justify-content-between">
        <h1><?= Html::encode($this->title) ?></h1>
        <span class="badge <?= $model->isSubmitted ? 'bg-success' : 'bg-secondary' ?>">
            <?= Html::encode($model->statusLabel) ?>
        </span>
    </div>

    <div class="detail-page-actions">
        <?php if (RbacHelper::isSupervisor()): ?>
            <?= Html::a('Student Report', ['report-student', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-warning', 'target' => '_blank']) ?>
            <?= Html::a('Office Report', ['report-office', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-warning', 'target' => '_blank']) ?>
            <?= Html::a('View Audit Log', ['audit-log', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-secondary']) ?>
        <?php endif; ?>

        <?php if (RbacHelper::isSupervisor()): ?>
            <?= Html::a('Grade All Competencies', ['grade-grid', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-success', 'title' => 'Grade all 12 competence areas']) ?>
            <?= Html::a('Update', ['update', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Add Single Grade', ['/grade/create', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-info']) ?>
            <?= Html::a('Submit Report', ['submit', 'assessment_id' => $model->assessment_id], [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => 'Submit this assessment report? Once submitted it will be flagged as completed.',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Delete', ['delete', 'assessment_id' => $model->assessment_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'detail-view'],
        'attributes' => [
            'assessment_id',
            [
                'attribute' => 'student_reg_no',
                'label' => 'Student Registration'
            ],
            [
                'attribute' => 'examiner_user_id',
                'value' => $model->examiner ? $model->examiner->name : 'N/A'
            ],
            [
                'attribute' => 'school_id',
                'value' => $model->school ? $model->school->school_name : 'N/A'
            ],
            [
                'attribute' => 'learning_area_id',
                'value' => $model->learningArea ? $model->learningArea->learning_area_name : 'N/A'
            ],
            'assessment_date',
            'start_time',
            'end_time',
            [
                'attribute' => 'total_score',
                'value' => "{$model->total_score}/100"
            ],
            [
                'attribute' => 'overall_level',
                'value' => function($model) {
                    $badges = [
                        'BE' => '<span class="badge bg-danger">BE - Below Expectations (0-39)</span>',
                        'AE' => '<span class="badge bg-warning text-dark">AE - Approaching Expectations (40-54)</span>',
                        'ME' => '<span class="badge bg-info">ME - Meets Expectations (55-79)</span>',
                        'EE' => '<span class="badge bg-success">EE - Exceeds Expectations (80-100)</span>'
                    ];
                    return $badges[$model->overall_level] ?? $model->overall_level;
                },
                'format' => 'html'
            ],
        ],
    ]) ?>

    <hr>
    <h3>Grades per Competence Area</h3>
    
    <?php if($model->grades && count($model->grades) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Competence Area</th>
                        <th>Score</th>
                        <th>Level</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($model->grades as $grade): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $grade->competenceArea ? $grade->competenceArea->competence_name : 'N/A' ?></td>
                            <td><?= $grade->score ?>/10</td>
                            <td>
                                <?php
                                $badges = [
                                    'BE' => '<span class="badge bg-danger">BE</span>',
                                    'AE' => '<span class="badge bg-warning text-dark">AE</span>',
                                    'ME' => '<span class="badge bg-info">ME</span>',
                                    'EE' => '<span class="badge bg-success">EE</span>'
                                ];
                                echo $badges[$grade->level] ?? $grade->level;
                                ?>
                            </td>
                            <td><?= Html::encode(substr($grade->remarks ?? '', 0, 50)) ?></td>
                            <td>
                                <?= Html::a('View', ['/grade/view', 'grade_id' => $grade->grade_id], ['class' => 'btn btn-sm btn-info']) ?>
                                <?php if (RbacHelper::isSupervisor()): ?>
                                    <?= Html::a('Edit', ['/grade/update', 'grade_id' => $grade->grade_id], ['class' => 'btn btn-sm btn-primary']) ?>
                                    <?= Html::a('Delete', ['/grade/delete', 'grade_id' => $grade->grade_id], ['class' => 'btn btn-sm btn-danger', 'data' => ['confirm' => 'Sure?', 'method' => 'post']]) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            No grades added yet.
            <?php if (RbacHelper::isSupervisor()): ?>
                <?= Html::a('Add grades', ['/grade/create', 'assessment_id' => $model->assessment_id], ['class' => 'alert-link']) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <hr>
    <h3>Assessment Images</h3>
    
    <?php
    $images = AssessmentImageBehavior::getImages($model->assessment_id);
    if (count($images) > 0): ?>
        <div class="alert alert-info">
            <?= count($images) ?>/<?= AssessmentImageBehavior::MAX_IMAGES ?> images uploaded
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <?php foreach ($images as $image): ?>
                <div class="card">
                    <img src="<?= AssessmentImageBehavior::getImageUrl($model->assessment_id, $image) ?>" 
                         class="card-img-top" 
                         alt="Assessment image"
                         style="height: 150px; object-fit: cover;">
                    <div class="card-body">
                        <small class="card-text"><?= $image ?></small><br>
                        <?php if (!\app\components\RbacHelper::isTpOffice()): ?>
                            <?= Html::a('Delete', ['delete-image', 'assessment_id' => $model->assessment_id, 'filename' => $image], [
                                'class' => 'btn btn-sm btn-danger mt-2',
                                'data' => ['confirm' => 'Delete this image?', 'method' => 'post']
                            ]) ?>
                        <?php else: ?>
                            <span class="text-muted">No action permitted</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary">
            No images uploaded yet.
        </div>
    <?php endif; ?>

    <?php if (!\app\components\RbacHelper::isTpOffice()): ?>
        <div class="mt-3">
            <?= Html::a('Add image', ['upload-images', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-primary']) ?>
        </div>
    <?php endif; ?>

    <?php if (\app\components\AssessmentImageBehavior::getImages($model->assessment_id) && \app\components\RbacHelper::isTpOffice()): ?>
        <div class="mt-3">
            <span class="text-muted">TP Office users cannot upload images.</span>
        </div>
    <?php endif; ?>

</div>
