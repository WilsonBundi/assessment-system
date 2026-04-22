<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Monitor Assessments';
$this->params['breadcrumbs'][] = ['label' => 'Department Chair Profile', 'url' => ['/department-chair/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="monitor-assessments">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Assessments Overview</h5>
        </div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'assessment_id',
                    'student_reg_no',
                    [
                        'attribute' => 'school_id',
                        'value' => function ($model) {
                            return $model->school ? $model->school->school_name : 'N/A';
                        },
                    ],
                    'assessment_date',
                    [
                        'attribute' => 'examiner_user_id',
                        'value' => function ($model) {
                            return $model->examinerUser ? $model->examinerUser->name : 'N/A';
                        },
                    ],
                    [
                        'attribute' => 'total_score',
                        'label' => 'Score',
                    ],
                    [
                        'attribute' => 'overall_level',
                        'label' => 'Level',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if (!$model->overall_level) {
                                return '<span class="badge badge-warning">Incomplete</span>';
                            }
                            return '<span class="badge badge-success">' . $model->overall_level . '</span>';
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<i class="fas fa-eye"></i>', ['/assessment/view', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-sm btn-info']);
                            }
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <div class="mt-3">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Profile', ['/department-chair/profile'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>
