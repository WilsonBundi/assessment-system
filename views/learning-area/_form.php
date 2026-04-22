<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\LearningArea $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="learning-area-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'learning_area_name')->textInput(['maxlength' => true, 'placeholder' => 'e.g., Mathematics, Science, Language']) ?>

    <div class="btn-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
