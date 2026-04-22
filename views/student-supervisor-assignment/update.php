<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\StudentSupervisorAssignment $model */

$this->title = 'Update Student Supervisor Assignment: ' . $model->assignment_id;
$this->params['breadcrumbs'][] = ['label' => 'Student Supervisor Assignments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->assignment_id, 'url' => ['view', 'assignment_id' => $model->assignment_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="student-supervisor-assignment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
