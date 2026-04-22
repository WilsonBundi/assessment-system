<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\GradeSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="search-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'grade_id')->textInput(['placeholder' => 'Search by Grade ID']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'assessment_id')->textInput(['placeholder' => 'Search by Assessment']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'competence_id')->textInput(['placeholder' => 'Search by Competence']) ?>
        </div>
    </div>

    <div class="btn-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
