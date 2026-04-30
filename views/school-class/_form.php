<?php

use app\models\School;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SchoolClass $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="school-class-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'school_id')->dropDownList(
        ArrayHelper::map(School::find()->orderBy(['school_name' => SORT_ASC])->all(), 'school_id', 'school_name'),
        ['prompt' => 'Select School...']
    ) ?>

    <?= $form->field($model, 'class_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
