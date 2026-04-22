<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Edit Department Chair Profile';
$this->params['breadcrumbs'][] = ['label' => 'Department Chair Profile', 'url' => ['/department-chair/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="edit-department-chair-profile">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'payroll_no')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save Profile', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Back', ['/department-chair/profile'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
