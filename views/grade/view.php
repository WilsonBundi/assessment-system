<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Grade;
use app\components\RbacHelper;

/** @var yii\web\View $this */
/** @var app\models\Grade $model */

$this->title = "Grade " . $model->grade_id;
$this->params['breadcrumbs'][] = ['label' => 'Grades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="grade-view">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="detail-page-actions">
        <?php if (RbacHelper::isSupervisor()): ?>
            <?= Html::a('Update', ['update', 'grade_id' => $model->grade_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'grade_id' => $model->grade_id], [
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
            'grade_id',
            [
                'attribute' => 'assessment_id',
                'value' => $model->assessment ? "REG: {$model->assessment->student_reg_no}" : 'N/A'
            ],
            [
                'attribute' => 'competence_id',
                'value' => $model->competenceArea ? $model->competenceArea->competence_name : 'N/A'
            ],
            [
                'attribute' => 'score',
                'value' => "{$model->score}/10"
            ],
            [
                'attribute' => 'level',
                'value' => function($model) {
                    $levels = [
                        'BE' => '<span class="badge bg-danger">BE - Below Expectations</span>',
                        'AE' => '<span class="badge bg-warning text-dark">AE - Approaching Expectations</span>',
                        'ME' => '<span class="badge bg-info">ME - Meets Expectations</span>',
                        'EE' => '<span class="badge bg-success">EE - Exceeds Expectations</span>'
                    ];
                    return $levels[$model->level] ?? $model->level;
                },
                'format' => 'html'
            ],
            'remarks:ntext',
        ],
    ]) ?>

</div>
