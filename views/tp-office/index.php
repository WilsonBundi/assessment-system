<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $totalAssessments int */
/* @var $completedAssessments int */
/* @var $recentAssessments array */
/* @var $schoolsCount int */
/* @var $zonesCount int */
/* @var $gradesCount int */

$this->title = 'TP Office Dashboard';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
    .actions-row {
        display: flex;
        gap: 15px;
        flex-wrap: nowrap;
        justify-content: space-between;
        margin-top: 25px;
        margin-bottom: 25px;
    }

    .action-card {
        flex: 1;
        min-width: 0;
        padding: 14px 18px;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border-radius: 10px;
        color: white;
        text-align: center;
        text-decoration: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.12);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.18);
    }

    .action-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 8px;
        opacity: 0.92;
    }

    .action-button {
        font-size: 0.95rem;
        font-weight: 700;
    }
');

// Register real-time update script
$this->registerJs("
    var lastUpdate = Date.now();
    var updateInterval = 30000; // 30 seconds

    function updateDashboard() {
        $.ajax({
            url: '" . \yii\helpers\Url::to(['tp-office/get-dashboard-data']) . "',
            type: 'GET',
            data: { last_update: lastUpdate },
            success: function(data) {
                if (data.updated) {
                    // Update assessment count silently
                    var currentCount = parseInt($('.bg-primary h2').text());
                    var newCount = data.totalAssessments;
                    if (currentCount !== newCount) {
                        $('.bg-primary h2').text(newCount);
                    }

                    // Update completed assessments count silently
                    var currentCompletedCount = parseInt($('.bg-secondary h2').text());
                    var newCompletedCount = data.completedAssessments;
                    if (currentCompletedCount !== newCompletedCount) {
                        $('.bg-secondary h2').text(newCompletedCount);
                    }

                    // Update recent assessments table silently
                    if (data.recentAssessmentsHtml) {
                        $('.table-responsive').html(data.recentAssessmentsHtml);
                    }

                    lastUpdate = Date.now();
                }
            },
            error: function() {
                console.log('Failed to update dashboard');
            }
        });
    }

    // Start polling
    setInterval(updateDashboard, updateInterval);

    // Initial update after 5 seconds
    setTimeout(updateDashboard, 5000);
", \yii\web\View::POS_READY);
?>


<div class="tp-office-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Assessments</h5>
                    <h2><?= $totalAssessments ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed Assessments</h5>
                    <h2><?= $completedAssessments ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Schools</h5>
                    <h2><?= $schoolsCount ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Zones</h5>
                    <h2><?= $zonesCount ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Grades</h5>
                    <h2><?= $gradesCount ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="actions-row">
        <a href="<?= \yii\helpers\Url::to(['reports']) ?>" class="action-card">
            <div class="action-title">Assessment Management</div>
            <div class="action-button">VIEW REPORT</div>
        </a>
        <a href="<?= \yii\helpers\Url::to(['/tp-office/master-data']) ?>" class="action-card">
            <div class="action-title">Master Data Management</div>
            <div class="action-button">MASTER DATA</div>
        </a>
        <a href="<?= \yii\helpers\Url::to(['/tp-office/student-assignments']) ?>" class="action-card">
            <div class="action-title">Supervisor Assignment</div>
            <div class="action-button">ASSIGN SUPERVISORS</div>
        </a>
        <a href="<?= \yii\helpers\Url::to(['zone-coordinator-assign']) ?>" class="action-card">
            <div class="action-title">Zone Coordinator Assignment</div>
            <div class="action-button">ASSIGN COORDINATORS</div>
        </a>
    </div>

    <!-- Recent Assessments -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Assessments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <?= $this->render('_recent_assessments_table', ['recentAssessments' => $recentAssessments]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>