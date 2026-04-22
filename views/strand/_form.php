<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\LearningArea;

/** @var yii\web\View $this */
/** @var app\models\Strand $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="strand-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'learning_area_id')->dropDownList(
        ArrayHelper::map(LearningArea::find()->all(), 'learning_area_id', 'learning_area_name'),
        ['prompt' => 'Select Learning Area...', 'class' => 'form-control']
    ) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Strand name']) ?>

    <div class="btn-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
