<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use Yii;

/* @var $this yii\web\View */
/* @var $zones array */
/* @var $supervisors array */
/* @var $searchQuery string */

$this->title = 'Manage Supervisor Zones';
$this->params['breadcrumbs'][] = ['label' => 'Student Assignments', 'url' => ['student-assignments']];
$this->params['breadcrumbs'][] = $this->title;

$zoneOptions = ArrayHelper::map($zones, 'zone_id', 'zone_name');
?>

<div class="supervisor-zones-container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-cog me-2"></i>Supervisor Zone Assignments
                        </h4>
                    </div>
                    <div>
                        <?= Html::a('Back to Assignments', ['student-assignments'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <div class="mb-4">
                        <form method="GET" action="" class="row gx-2 align-items-end">
                            <div class="col-md-9">
                                <label class="form-label fw-bold mb-2">
                                    <i class="fas fa-search me-1"></i>Search Supervisors
                                </label>
                                <input 
                                    type="text" 
                                    name="search" 
                                    class="form-control form-control-lg" 
                                    placeholder="Search by name, email, username or payroll number..." 
                                    value="<?= Html::encode($searchQuery ?? '') ?>"
                                >
                            </div>
                            <div class="col-md-3 text-end">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </form>
                        <?php if (!empty($searchQuery)): ?>
                            <div class="mt-2">
                                <?= Html::a(
                                    '<i class="fas fa-times me-1"></i>Clear Search',
                                    ['supervisor-zones'],
                                    ['class' => 'btn btn-outline-secondary btn-sm']
                                ) ?>
                                <span class="text-muted ms-2">
                                    Found <?= count($supervisors) ?> result<?= count($supervisors) !== 1 ? 's' : '' ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($supervisors)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <?= !empty($searchQuery) ? 'No supervisors found matching your search.' : 'No supervisors found.' ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40%;">Supervisor</th>
                                        <th style="width: 25%;">Email</th>
                                        <th style="width: 25%;">Zone</th>
                                        <th style="width: 10%;" class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($supervisors as $supervisor): ?>
                                        <tr>
                                            <td>
                                                <strong><?= Html::encode($supervisor->name) ?></strong><br>
                                                <small class="text-muted">Payroll No: <?= Html::encode($supervisor->payroll_no ?? 'N/A') ?></small>
                                            </td>
                                            <td><?= Html::encode($supervisor->email ?? 'N/A') ?></td>
                                            <td>
                                                <form method="post" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/update-supervisor-zone'])) ?>" class="d-flex gap-2 align-items-center mb-0">
                                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                    <?= Html::hiddenInput('supervisor_user_id', $supervisor->user_id) ?>
                                                    <?= Html::dropDownList('zone_id', $supervisor->zone_id, $zoneOptions, [
                                                        'class' => 'form-select form-select-sm searchable-select',
                                                        'data-placeholder' => '-- Choose a zone --',
                                                        'required' => true,
                                                    ]) ?>
                                            </td>
                                            <td class="text-end">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-save me-1"></i>Save
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .supervisor-zones-container .form-control-lg {
        min-height: 48px;
        font-size: 1rem;
    }

    .supervisor-zones-container .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }

    .supervisor-zones-container .card {
        border-radius: 0.5rem;
    }

    .supervisor-zones-container .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        vertical-align: middle;
        font-weight: 600;
    }

    .supervisor-zones-container .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .supervisor-zones-container .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .supervisor-zones-container .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .supervisor-zones-container .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0b5ed7;
    }
</style>
