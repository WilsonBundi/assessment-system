<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\StudentSupervisorAssignment $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="student-supervisor-assignment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'student_reg_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'supervisor_user_id')->textInput() ?>

    <?= $form->field($model, 'assigned_by')->textInput() ?>

    <?= $form->field($model, 'assigned_at')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zone_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
