<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\StrandSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="search-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'strand_id')->textInput(['placeholder' => 'Search by Strand ID']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'learning_area_id')->textInput(['placeholder' => 'Search by Learning Area']) ?>
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
