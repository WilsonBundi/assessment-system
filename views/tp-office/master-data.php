<?php
use yii\helpers\Html;

$this->title = 'Master Data Management';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="master-data">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-th-large fa-3x text-primary mb-3"></i>
                    <h3 class="text-primary">Master Data</h3>
                    <p class="text-muted mb-3">Overview</p>
                    <div class="btn-group-vertical w-100">
                        <?= Html::a('<i class="fas fa-eye"></i> View Overview', ['/tp-office/master-data'], ['class' => 'btn btn-outline-primary mb-1']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Dashboard', ['index'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-map-marked-alt fa-3x text-success mb-3"></i>
                    <h3 class="text-success"><?= $stats['zones'] ?></h3>
                    <p class="text-muted mb-3">Zones</p>
                    <div class="btn-group-vertical w-100">
                        <?= Html::a('<i class="fas fa-list"></i> View All', ['/tp-office/zones'], ['class' => 'btn btn-outline-success mb-1']) ?>
                        <?= Html::a('<i class="fas fa-plus"></i> Add New', ['/zone/create'], ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-school fa-3x text-primary mb-3"></i>
                    <h3 class="text-primary"><?= $stats['schools'] ?></h3>
                    <p class="text-muted mb-3">Schools</p>
                    <div class="btn-group-vertical w-100">
                        <?= Html::a('<i class="fas fa-list"></i> View All', ['/tp-office/schools'], ['class' => 'btn btn-outline-primary mb-1']) ?>
                        <?= Html::a('<i class="fas fa-plus"></i> Add New', ['/school/create'], ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-graduation-cap fa-3x text-info mb-3"></i>
                    <h3 class="text-info"><?= $stats['grades'] ?></h3>
                    <p class="text-muted mb-3">Grades</p>
                    <div class="btn-group-vertical w-100">
                        <?= Html::a('<i class="fas fa-list"></i> View All', ['/tp-office/grades'], ['class' => 'btn btn-outline-info mb-1']) ?>
                        <?= Html::a('<i class="fas fa-plus"></i> Add New', ['/grade/create'], ['class' => 'btn btn-info']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x text-warning mb-3"></i>
                    <h3 class="text-warning"><?= $stats['learningAreas'] ?></h3>
                    <p class="text-muted mb-3">Learning Areas</p>
                    <div class="btn-group-vertical w-100">
                        <?= Html::a('<i class="fas fa-list"></i> View All', ['/tp-office/learning-areas'], ['class' => 'btn btn-outline-warning mb-1']) ?>
                        <?= Html::a('<i class="fas fa-plus"></i> Add New', ['/learning-area/create'], ['class' => 'btn btn-warning']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                    <h3 class="text-primary"><?= $stats['classes'] ?></h3>
                    <p class="text-muted mb-3">School Classes</p>
                    <div class="btn-group-vertical w-100">
                        <?= Html::a('<i class="fas fa-list"></i> View All', ['/school-class/index'], ['class' => 'btn btn-outline-primary mb-1']) ?>
                        <?= Html::a('<i class="fas fa-plus"></i> Add New', ['/school-class/create'], ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-stream fa-3x text-danger mb-3"></i>
                    <h3 class="text-danger"><?= $stats['strands'] ?></h3>
                    <p class="text-muted mb-3">Strands</p>
                    <div class="btn-group-vertical w-100">
                        <?= Html::a('<i class="fas fa-list"></i> View All', ['/tp-office/strands'], ['class' => 'btn btn-outline-danger mb-1']) ?>
                        <?= Html::a('<i class="fas fa-plus"></i> Add New', ['/strand/create'], ['class' => 'btn btn-danger']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-code-branch fa-3x text-secondary mb-3"></i>
                    <h3 class="text-secondary"><?= $stats['substrands'] ?></h3>
                    <p class="text-muted mb-3">Sub-Strands</p>
                    <div class="btn-group-vertical w-100">
                        <?= Html::a('<i class="fas fa-list"></i> View All', ['/tp-office/substrands'], ['class' => 'btn btn-outline-secondary mb-1']) ?>
                        <?= Html::a('<i class="fas fa-plus"></i> Add New', ['/substrand/create'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Quick Actions</h5>
                    <p class="text-muted">Manage users and system settings</p>
                    <div class="btn-group" role="group">
                        <?= Html::a('<i class="fas fa-users"></i> Manage Users', ['/users/index'], ['class' => 'btn btn-outline-primary']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Dashboard', ['index'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>