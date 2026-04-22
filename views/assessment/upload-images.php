<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\AssessmentImageBehavior;

/** @var yii\web\View $this */
/** @var app\models\Assessment $model */

$this->title = 'Upload Images: ' . $model->student_reg_no;
$this->params['breadcrumbs'][] = ['label' => 'Assessments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->assessment_id, 'url' => ['view', 'assessment_id' => $model->assessment_id]];
$this->params['breadcrumbs'][] = 'Upload Images';
?>

<div class="assessment-upload-images">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <p class="text-muted">You can upload up to <?= AssessmentImageBehavior::MAX_IMAGES ?> images total (already uploaded: <?= count(AssessmentImageBehavior::getImages($model->assessment_id)) ?>).</p>

            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data']
            ]); ?>

            <?= Html::fileInput('images[]', null, [
                'multiple' => true,
                'accept' => 'image/jpeg,image/png,image/gif,image/webp',
                'class' => 'form-control'
            ]) ?>

            <div class="mt-3">
                <?= Html::submitButton('Upload', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Back to Assessment', ['view', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-secondary ms-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Current Images</h5>
            <?= AssessmentImageBehavior::getImageGallery($model->assessment_id) ?>
        </div>
    </div>
</div>
