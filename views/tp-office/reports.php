<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $currentStatus string */

$this->title = 'Assessment Reports';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
/* Real-time update styles removed for clean user experience */
</style>

<div class="assessment-reports">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="btn-group mb-3" role="group">
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-download me-1"></i>Download Excel', ['tp-office/download-assessment-reports-excel', 'status' => $currentStatus], ['class' => 'btn btn-success']) ?>
    </div>

    <!-- Status Filter Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <?= Html::a('All Assessments', ['reports', 'status' => 'all'], [
                'class' => 'nav-link ' . ($currentStatus === 'all' ? 'active' : '')
            ]) ?>
        </li>
        <li class="nav-item">
            <?= Html::a('Completed', ['reports', 'status' => 'completed'], [
                'class' => 'nav-link ' . ($currentStatus === 'completed' ? 'active' : '')
            ]) ?>
        </li>
        <li class="nav-item">
            <?= Html::a('Pending', ['reports', 'status' => 'pending'], [
                'class' => 'nav-link ' . ($currentStatus === 'pending' ? 'active' : '')
            ]) ?>
        </li>
    </ul>

    <div id="reports-container">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped table-bordered'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'assessment_id',
                    'label' => 'Assessment',
                ],
                'student_reg_no',
                [
                    'label' => 'Student Name',
                    'value' => function ($model) {
                        return $model->student ? $model->student->getName() : 'N/A';
                    },
                ],
                [
                    'label' => 'Student Phone',
                    'value' => function ($model) {
                        return $model->student ? ($model->student->phone_no ?: 'N/A') : 'N/A';
                    },
                ],
                [
                    'label' => 'Student Email',
                    'value' => function ($model) {
                        return $model->student ? ($model->student->email ?: 'N/A') : 'N/A';
                    },
                ],
                [
                    'attribute' => 'school_id',
                    'label' => 'School',
                    'value' => function ($model) {
                        return $model->school->school_name ?? 'N/A';
                    },
                ],
                'assessment_date',
                [
                    'attribute' => 'examiner_user_id',
                    'label' => 'Examiner',
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
</div>

<?php
$this->registerJs("
var lastUpdate = Date.now();
var updateInterval = 30000; // 30 seconds
var isUpdating = false;

function updateReportsData() {
    if (isUpdating) return;

    isUpdating = true;

    $.ajax({
        url: '" . \yii\helpers\Url::to(['tp-office/get-reports-data']) . "',
        type: 'GET',
        data: { 
            last_update: lastUpdate,
            status: '" . $currentStatus . "'
        },
        success: function(response) {
            lastUpdate = response.timestamp;

            if (response.updated) {
                // Update the reports table silently
                $('#reports-container').html(response.reportsTableHtml);
            }
        },
        error: function() {
            console.log('Failed to update reports');
        },
        complete: function() {
            isUpdating = false;
        }
    });
}

// Start polling
setInterval(updateReportsData, updateInterval);
", \yii\web\View::POS_READY);
?>
