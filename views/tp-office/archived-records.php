<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Archived Assessment Records';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="archived-assessment-records">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('View Active Reports', ['reports'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-secondary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
                'attribute' => 'archived_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'label' => 'Archived Date',
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {unarchive}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('View', ['view', 'id' => $model->assessment_id], ['class' => 'btn btn-sm btn-info']);
                    },
                    'unarchive' => function ($url, $model, $key) {
                        return Html::a('Restore', ['unarchive', 'id' => $model->assessment_id], [
                            'class' => 'btn btn-sm btn-warning',
                            'data-confirm' => 'Are you sure you want to restore this assessment?',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
