<?php

/** @var yii\web\View $this */
/** @var array $stats Dashboard statistics from session */

use yii\bootstrap5\Html;
use app\components\RbacHelper;
use app\models\Assessment;

$this->title = 'Dashboard';
?>

<style>
    .dashboard-page {
        padding: 30px 0;
    }

    .dashboard-section {
        margin-bottom: 40px;
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid #3498db;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #3498db;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 25px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        border-left: 4px solid #3498db;
        transition: transform 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-5px);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #3498db;
        margin-bottom: 8px;
    }

    .stat-label {
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .actions-row {
        display: flex;
        gap: 15px;
        flex-wrap: nowrap;
        justify-content: space-between;
    }

    .action-card {
        flex: 1;
        min-width: 0;
        padding: 12px 15px;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border-radius: 8px;
        color: white;
        text-align: center;
        text-decoration: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        color: white;
        text-decoration: none;
    }

    .action-title {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 8px;
        opacity: 0.9;
    }

    .action-button {
        font-size: 0.95rem;
        font-weight: 700;
    }
</style>

<div class="dashboard-page">
    <div class="container">

        <!-- SYSTEM OVERVIEW STATISTICS -->
        <div class="dashboard-section">
            <h3 class="section-title"><i class="fas fa-chart-bar"></i> System Overview</h3>
            <div class="stats-grid">
                <?php
                $totalAssessments = Assessment::find()->count();
                $pendingValidation = Assessment::find()
                    ->andWhere(['archived' => 1])
                    ->andWhere(['is', 'validated_by', null])
                    ->count();
                $inProgressAssessments = Assessment::find()
                    ->andWhere(['or', ['archived' => 0], ['archived' => null]])
                    ->andWhere(['is', 'validated_by', null])
                    ->count();
                $validatedAssessments = Assessment::find()->andWhere(['is not', 'validated_by', null])->count();
                $schoolCount = Assessment::find()->select(['school_id'])->distinct()->count('DISTINCT school_id');
                ?>
                <div class="stat-item">
                    <div class="stat-value"><?= $totalAssessments ?></div>
                    <div class="stat-label">Total Assessments</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $pendingValidation ?></div>
                    <div class="stat-label">Pending Validation</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $inProgressAssessments ?></div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $validatedAssessments ?></div>
                    <div class="stat-label">Validated</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $schoolCount ?></div>
                    <div class="stat-label">Schools</div>
                </div>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="dashboard-section">
            <div class="actions-row">
                <?php if (RbacHelper::isTpOffice()) : ?>
                    <a href="<?= \yii\helpers\Url::to(['/tp-office/reports']) ?>" class="action-card">
                        <div class="action-title">Assessment Management</div>
                        <div class="action-button">VIEW REPORT</div>
                    </a>
                <?php endif; ?>
                
                <?php if (RbacHelper::isTpOffice()) : ?>
                    <a href="<?= \yii\helpers\Url::to(['/tp-office/master-data']) ?>" class="action-card">
                        <div class="action-title">Master Data Management</div>
                        <div class="action-button">MASTER DATA</div>
                    </a>
                <?php endif; ?>
                
                <?php if (RbacHelper::isTpOffice()) : ?>
                    <a href="<?= \yii\helpers\Url::to(['/tp-office/index']) ?>" class="action-card">
                        <div class="action-title">Supervisor Assignment</div>
                        <div class="action-button">ASSIGN SUPERVISORS</div>
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>