<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Zone;

/** @var yii\web\View $this */
/** @var app\models\School $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="school-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'school_code')->textInput(['maxlength' => true, 'placeholder' => 'e.g., SCH001']) ?>

    <?= $form->field($model, 'school_name')->textInput(['maxlength' => true, 'placeholder' => 'School name']) ?>

    <?= $form->field($model, 'zone_id')->dropDownList(
        ArrayHelper::map(Zone::find()->orderBy(['zone_name' => SORT_ASC])->all(), 'zone_id', 'zone_name'),
        ['prompt' => 'Select Zone...', 'class' => 'form-control searchable-select', 'data-placeholder' => 'Select Zone...']
    ) ?>

    <div class="btn-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
