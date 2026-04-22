<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\StudentSupervisorAssignmentSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="student-supervisor-assignment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'assignment_id') ?>

    <?= $form->field($model, 'student_reg_no') ?>

    <?= $form->field($model, 'supervisor_user_id') ?>

    <?= $form->field($model, 'school_id') ?>

    <?= $form->field($model, 'assigned_by') ?>

    <?php // echo $form->field($model, 'assigned_at') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'zone_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
