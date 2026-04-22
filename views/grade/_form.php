<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Assessment;
use app\models\CompetenceArea;
use app\models\Grade;

/** @var yii\web\View $this */
/** @var app\models\Grade $model */
/** @var yii\widgets\ActiveForm $form */

// Get grading scale for reference
$gradingScale = Grade::getGradingScale();
?>

<div class="grade-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'assessment_id')->dropDownList(
        ArrayHelper::map(Assessment::find()->all(), 'assessment_id', function($data) {
            return "REG: {$data->student_reg_no} - {$data->school->school_name}";
        }),
        ['prompt' => 'Select Assessment...', 'class' => 'form-control']
    ) ?>

    <?= $form->field($model, 'competence_id')->dropDownList(
        ArrayHelper::map(CompetenceArea::find()->all(), 'competence_id', 'competence_name'),
        ['prompt' => 'Select Competence Area...', 'class' => 'form-control']
    ) ?>

    <?= $form->field($model, 'score')->textInput(['type' => 'number', 'min' => '0', 'max' => '10', 'placeholder' => 'Score 0-10']) ?>

    <?= $form->field($model, 'level')->dropDownList(
        [
            'BE' => 'BE - Below Expectations (0-3)',
            'AE' => 'AE - Approaching Expectations (4-5)',
            'ME' => 'ME - Meets Expectations (6-7)',
            'EE' => 'EE - Exceeds Expectations (8-10)'
        ],
        ['prompt' => 'Select Performance Level...', 'class' => 'form-control']
    )->hint('Based on TP E24 Assessment Template') ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 4, 'placeholder' => 'Enter remarks or feedback']) ?>

    <div class="alert alert-info">
        <strong>Grading Scale:</strong><br>
        <small>
            <strong>BE:</strong> Below Expectations (0-3)<br>
            <strong>AE:</strong> Approaching Expectations (4-5)<br>
            <strong>ME:</strong> Meets Expectations (6-7)<br>
            <strong>EE:</strong> Exceeds Expectations (8-10)
        </small>
    </div>

    <div class="btn-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
