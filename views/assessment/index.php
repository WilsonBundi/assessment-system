<?php

use app\models\Assessment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\AssessmentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = app\components\RbacHelper::isZoneCoordinator() ? 'Assessment Management' : 'Assessments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assessment-index index-page">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if (app\components\RbacHelper::isZoneCoordinator()): ?>
            <p class="text-muted">Review, edit, and validate assessment reports</p>
        <?php endif; ?>
    </div>

    <div class="index-actions">
        <?php if (app\components\RbacHelper::isSupervisor()): ?>
            <?= Html::a('Add New Assessment', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <div class="search-form">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'assessment_id',
            'student_reg_no',
            [
                'attribute' => 'examiner_user_id',
                'value' => function($model) {
                    return $model->examiner ? $model->examiner->name : 'N/A';
                }
            ],
            [
                'attribute' => 'school_id',
                'value' => function($model) {
                    return $model->school ? $model->school->school_name : 'N/A';
                }
            ],
            [
                'attribute' => 'total_score',
                'value' => function($model) {
                    return "{$model->total_score}/100";
                }
            ],
            [
                'attribute' => 'overall_level',
                'value' => function($model) {
                    $badges = [
                        'BE' => '<span class="badge bg-danger">BE</span>',
                        'AE' => '<span class="badge bg-warning text-dark">AE</span>',
                        'ME' => '<span class="badge bg-info">ME</span>',
                        'EE' => '<span class="badge bg-success">EE</span>'
                    ];
                    return $badges[$model->overall_level] ?? '-';
                },
                'format' => 'html'
            ],
            [
                'label' => 'Validation Status',
                'value' => function($model) {
                    if ($model->validated_by) {
                        return '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Validated</span>';
                    } else {
                        return '<span class="badge bg-secondary"><i class="fas fa-clock"></i> Pending</span>';
                    }
                },
                'format' => 'html',
                'filter' => ['1' => 'Validated', '0' => 'Pending']
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{review} {edit} {validate} {view} {update} {delete}',
                'visibleButtons' => [
                    'review' => function($model) {
                        return app\components\RbacHelper::isZoneCoordinator();
                    },
                    'edit' => function($model) {
                        return app\components\RbacHelper::isZoneCoordinator() && !$model->validated_by;
                    },
                    'validate' => function($model) {
                        return app\components\RbacHelper::isZoneCoordinator() && $model->overall_level && !$model->validated_by;
                    },
                    'view' => function($model) {
                        return !app\components\RbacHelper::isZoneCoordinator();
                    },
                    'update' => function($model) {
                        return !app\components\RbacHelper::isZoneCoordinator();
                    },
                    'delete' => function($model) {
                        return !app\components\RbacHelper::isZoneCoordinator();
                    },
                ],
                'buttons' => [
                    'review' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i> Review', ['/zone-coordinator/review-assessment', 'assessment_id' => $model->assessment_id], [
                            'class' => 'btn btn-sm btn-info',
                            'title' => 'Review Assessment'
                        ]);
                    },
                    'edit' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-edit"></i> Edit', ['/zone-coordinator/edit-assessment', 'assessment_id' => $model->assessment_id], [
                            'class' => 'btn btn-sm btn-warning',
                            'title' => 'Edit Assessment'
                        ]);
                    },
                    'validate' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-check-circle"></i> Validate', ['/zone-coordinator/validate-assessment', 'assessment_id' => $model->assessment_id], [
                            'class' => 'btn btn-sm btn-success',
                            'title' => 'Validate Assessment'
                        ]);
                    },
                ],
                'urlCreator' => function ($action, Assessment $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'assessment_id' => $model->assessment_id]);
                 }
            ],
        ],
    ]); ?>


</div>
