<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\SchoolClass;

/** @var yii\web\View $this */
/** @var app\models\AssessmentSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="search-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'assessment_id')->textInput(['placeholder' => 'Search by Assessment ID']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'examiner_user_id')->textInput(['placeholder' => 'Search by Examiner']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'student_reg_no')->textInput(['placeholder' => 'Search by Student']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'class_id')->dropDownList(
                ArrayHelper::map(SchoolClass::find()->orderBy(['class_name' => SORT_ASC])->all(), 'class_id', 'class_name'),
                ['prompt' => 'Select Class...']
            ) ?>
        </div>
    </div>

    <div class="btn-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
