<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SubstrandSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="search-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'substrand_id')->textInput(['placeholder' => 'Search by Substrand ID']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'strand_id')->textInput(['placeholder' => 'Search by Strand']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'name')->textInput(['placeholder' => 'Search by Name']) ?>
        </div>
    </div>

    <div class="btn-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
