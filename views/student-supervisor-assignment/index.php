<?php

use app\models\StudentSupervisorAssignment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\StudentSupervisorAssignmentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Student Supervisor Assignments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-supervisor-assignment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Student Supervisor Assignment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'assignment_id',
            'student_reg_no',
            'supervisor_user_id',
            'school_id',
            'assigned_by',
            //'assigned_at',
            //'status',
            //'notes:ntext',
            //'zone_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, StudentSupervisorAssignment $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'assignment_id' => $model->assignment_id]);
                 }
            ],
        ],
    ]); ?>


</div>
