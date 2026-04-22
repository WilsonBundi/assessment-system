<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\LearningAreaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="search-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'learning_area_id')->textInput(['placeholder' => 'Search by Learning Area ID']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'learning_area_name')->textInput(['placeholder' => 'Search by Learning Area Name']) ?>
        </div>
    </div>

    <div class="btn-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
