<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="reports-table-container">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'assessment_id',
            'student_reg_no',
            [
                'attribute' => 'school_id',
                'value' => function ($model) {
                    return $model->school->school_name ?? 'N/A';
                },
            ],
            'assessment_date',
            [
                'attribute' => 'examiner_user_id',
                'value' => function ($model) {
                    return $model->examinerUser->name ?? 'N/A';
                },
            ],
            'overall_level',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="btn-group btn-group-sm" role="group">{view}{download}{archive}</div>',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i> View', ['view', 'id' => $model->assessment_id], [
                            'class' => 'btn btn-info',
                            'title' => 'View Assessment'
                        ]);
                    },
                    'download' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-download"></i> Download', ['download-report', 'id' => $model->assessment_id], [
                            'class' => 'btn btn-success',
                            'title' => 'Download Report'
                        ]);
                    },
                    'archive' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-archive"></i> Archive', $url, [
                            'class' => 'btn btn-danger',
                            'title' => 'Archive Assessment',
                            'data-confirm' => 'Are you sure you want to archive this assessment?',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>