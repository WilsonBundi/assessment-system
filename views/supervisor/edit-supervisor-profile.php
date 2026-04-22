<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Edit Supervisor Profile';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/site/dashboard']];
$this->params['breadcrumbs'][] = ['label' => 'Supervisor Profile', 'url' => ['profile']];
$this->params['breadcrumbs'][] = 'Edit Supervisor Profile';
?>

<div class="supervisor-edit">
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-edit"></i> Edit Supervisor Profile</h4>
                    </div>
                    <div class="card-body">

                        <?php $form = ActiveForm::begin([
                            'id' => 'supervisor-form',
                            'options' => ['class' => 'form-horizontal'],
                        ]); ?>

                        <!-- Name -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Full Name</label>
                            <div class="col-sm-9">
                                <?= $form->field($model, 'name', [
                                    'template' => '{input}{error}',
                                    'options' => ['class' => 'form-group row'],
                                ])->textInput(['class' => 'form-control']) ?>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Phone Number</label>
                            <div class="col-sm-9">
                                <?= $form->field($model, 'phone', [
                                    'template' => '{input}{error}',
                                    'options' => ['class' => 'form-group row'],
                                ])->textInput(['class' => 'form-control', 'placeholder' => 'e.g., +254712345678']) ?>
                            </div>
                        </div>

                        <!-- Payroll Number (Read-Only) -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Payroll Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="<?= Html::encode($model->payroll_no) ?>" readonly>
                                <small class="form-text text-muted">This cannot be changed</small>
                            </div>
                        </div>

                        <!-- Username (Read-Only) -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Username</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="<?= Html::encode($model->username) ?>" readonly>
                                <small class="form-text text-muted">This cannot be changed</small>
                            </div>
                        </div>

                        <!-- Status (Read-Only) -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Status</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="<?= Html::encode($model->status) ?>" readonly>
                                <small class="form-text text-muted">This cannot be changed</small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <?= Html::submitButton(
                                    '<i class="fas fa-save"></i> Save Changes',
                                    ['class' => 'btn btn-primary', 'name' => 'save-button']
                                ) ?>
                                <?= Html::a(
                                    '<i class="fas fa-times-circle"></i> Cancel',
                                    ['profile'],
                                    ['class' => 'btn btn-secondary ms-2']
                                ) ?>
                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .card-header {
        padding: 1rem;
    }

    .bg-primary {
        background-color: #5B9BD5 !important;
    }

    .form-group.row .col-form-label {
        font-weight: 600;
        color: #333;
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: #e9ecef;
        color: #6c757d;
    }

    .btn-primary {
        background-color: #5B9BD5;
        border-color: #5B9BD5;
    }

    .btn-primary:hover {
        background-color: #2E75B6;
        border-color: #2E75B6;
    }
</style>
