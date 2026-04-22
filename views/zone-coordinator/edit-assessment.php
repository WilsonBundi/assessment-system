<?php
use yii\helpers\Html;

$this->title = 'Edit Assessment - ' . $model->student_reg_no;
$this->params['breadcrumbs'][] = ['label' => 'Zone Coordinator Profile', 'url' => ['/zone-coordinator/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="edit-assessment">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Edit Assessment Report</h3>
        </div>
        <div class="card-body">
            <p class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                As Zone Coordinator, you can edit assessment details for accuracy and quality assurance.
                Note: Evidence images cannot be edited by Zone Coordinators.
            </p>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Student:</strong> <?= Html::encode($model->student_reg_no) ?></p>
                    <p><strong>School:</strong> <?= Html::encode($model->school ? $model->school->school_name : 'N/A') ?></p>
                    <p><strong>Assessment Date:</strong> <?= Html::encode($model->assessment_date) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Examiner:</strong> <?= Html::encode($model->examinerUser ? $model->examinerUser->name : 'N/A') ?></p>
                    <p><strong>Total Score:</strong> <?= $model->total_score ?? 'N/A' ?></p>
                    <p><strong>Overall Level:</strong> <span class="badge badge-info"><?= $model->overall_level ?? 'N/A' ?></span></p>
                </div>
            </div>

            <hr>

            <div class="alert alert-warning">
                <strong>Editable Fields:</strong> You can modify basic assessment information for accuracy and completeness.
            </div>

            <?php $form = \yii\widgets\ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'student_reg_no') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'assessment_date') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'learning_area_id')->dropDownList(
                        \yii\helpers\ArrayHelper::map(\app\models\LearningArea::find()->all(), 'learning_area_id', 'learning_area_name'),
                        ['prompt' => 'Select Learning Area...']
                    ) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'school_id')->dropDownList(
                        \yii\helpers\ArrayHelper::map(\app\models\School::find()->all(), 'school_id', 'school_name'),
                        ['prompt' => 'Select School...']
                    ) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save Changes', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Cancel', ['review-assessment', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>
</div>
