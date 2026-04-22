<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SchoolSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="search-form">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form']
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'school_code')->textInput(['placeholder' => 'Search by code...'])->label('School Code') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'school_name')->textInput(['placeholder' => 'Search by name...'])->label('School Name') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'zone_id')->textInput(['placeholder' => 'Zone ID...'])->label('Zone') ?>
        </div>
    </div>

    <div class="btn-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Clear', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
