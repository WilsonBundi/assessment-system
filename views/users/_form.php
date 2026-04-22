<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Role;

/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var yii\widgets\ActiveForm $form */

$statusOptions = [
    'active' => 'Active',
    'inactive' => 'Inactive',
    'suspended' => 'Suspended',
];
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'role_id')->dropDownList(
        ArrayHelper::map(Role::find()->all(), 'role_id', 'role_name'),
        ['prompt' => 'Select Role...', 'class' => 'form-control']
    ) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Unique username']) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'placeholder' => 'Enter password']) ?>

    <?= $form->field($model, 'payroll_no')->textInput(['type' => 'number', 'placeholder' => 'Staff payroll number']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Full name']) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'type' => 'tel', 'placeholder' => 'Phone number']) ?>

    <?= $form->field($model, 'status')->dropDownList(
        $statusOptions,
        ['class' => 'form-control']
    ) ?>

    <div class="btn-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
