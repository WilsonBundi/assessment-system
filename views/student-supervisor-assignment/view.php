<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\StudentSupervisorAssignment $model */

$this->title = $model->assignment_id;
$this->params['breadcrumbs'][] = ['label' => 'Student Supervisor Assignments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="student-supervisor-assignment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'assignment_id' => $model->assignment_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'assignment_id' => $model->assignment_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'assignment_id',
            'student_reg_no',
            'supervisor_user_id',
            'school_id',
            'assigned_by',
            'assigned_at',
            'status',
            'notes:ntext',
            'zone_id',
        ],
    ]) ?>

</div>
