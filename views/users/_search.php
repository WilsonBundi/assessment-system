<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UsersSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="search-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'user_id')->textInput(['placeholder' => 'Search by User ID']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'username')->textInput(['placeholder' => 'Search by Username']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'role_id')->textInput(['placeholder' => 'Search by Role']) ?>
        </div>
    </div>

    <div class="btn-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
