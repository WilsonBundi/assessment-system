<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\CompetenceArea $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="competence-area-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'competence_name')->textInput(['maxlength' => true, 'placeholder' => 'Competence area name']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6, 'placeholder' => 'Detailed description of the competence area']) ?>

    <div class="btn-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
