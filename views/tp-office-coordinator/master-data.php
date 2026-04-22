<?php
use yii\helpers\Html;

$this->title = 'Master Data Configuration';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Profile', 'url' => ['/tp-office-coordinator/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="master-data">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary"><?= $stats['schools'] ?></h3>
                    <p class="text-muted">Schools</p>
                    <?= Html::a('Manage', ['/school/index'], ['class' => 'btn btn-sm btn-primary']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success"><?= $stats['zones'] ?></h3>
                    <p class="text-muted">Zones</p>
                    <?= Html::a('Manage', ['/zone/index'], ['class' => 'btn btn-sm btn-success']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info"><?= $stats['grades'] ?></h3>
                    <p class="text-muted">Grades</p>
                    <?= Html::a('Manage', ['/grade/index'], ['class' => 'btn btn-sm btn-info']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning"><?= $stats['learningAreas'] ?></h3>
                    <p class="text-muted">Learning Areas</p>
                    <?= Html::a('Manage', ['/learning-area/index'], ['class' => 'btn btn-sm btn-warning']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-danger"><?= $stats['competenceAreas'] ?></h3>
                    <p class="text-muted">Competence Areas</p>
                    <?= Html::a('Manage', ['/competence-area/index'], ['class' => 'btn btn-sm btn-danger']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-secondary"><?= $stats['strands'] ?></h3>
                    <p class="text-muted">Strands</p>
                    <?= Html::a('Manage', ['/strand/index'], ['class' => 'btn btn-sm btn-secondary']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info"><?= $stats['substrands'] ?></h3>
                    <p class="text-muted">Substrands</p>
                    <?= Html::a('Manage', ['/substrand/index'], ['class' => 'btn btn-sm btn-info']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary"><?= $stats['users'] ?></h3>
                    <p class="text-muted">System Users</p>
                    <?= Html::a('Manage', ['/users/index'], ['class' => 'btn btn-sm btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Profile', ['/tp-office-coordinator/profile'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>
