<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\RoleSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="search-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'role_id')->textInput(['placeholder' => 'Search by Role ID']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'role_name')->textInput(['placeholder' => 'Search by Role Name']) ?>
        </div>
    </div>

    <div class="btn-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
