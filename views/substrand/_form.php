<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Strand;

/** @var yii\web\View $this */
/** @var app\models\Substrand $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="substrand-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'strand_id')->dropDownList(
        ArrayHelper::map(Strand::find()->all(), 'strand_id', 'name'),
        ['prompt' => 'Select Strand...', 'class' => 'form-control']
    ) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Substrand name']) ?>

    <div class="btn-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
