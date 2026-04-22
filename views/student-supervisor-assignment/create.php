<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\StudentSupervisorAssignment $model */

$this->title = 'Create Student Supervisor Assignment';
$this->params['breadcrumbs'][] = ['label' => 'Student Supervisor Assignments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-supervisor-assignment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
