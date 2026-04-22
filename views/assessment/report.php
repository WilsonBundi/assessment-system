<?php

use yii\helpers\Html;
use app\models\Grade;

/** @var yii\web\View $this */
/** @var app\models\Assessment $model */
/** @var bool $showMarks Whether to show marks and scores (Office Copy) or just feedback (Student Copy) */

$showMarks = $showMarks ?? false;
$reportType = $showMarks ? 'OFFICE COPY (WITH MARKS)' : 'STUDENT COPY (FEEDBACK ONLY)';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TP Assessment Report - <?= $model->student_reg_no ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .report-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        .report-header {
            text-align: center;
            border-bottom: 3px solid #2E75B6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .report-header h1 {
            font-size: 24px;
            color: #2E75B6;
            margin-bottom: 5px;
        }
        .report-type {
            font-size: 14px;
            font-weight: bold;
            color: #d9534f;
            background: #f9f9f9;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-weight: bold;
            color: #2E75B6;
            font-size: 12px;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 14px;
        }
        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .assessment-table thead {
            background: #5B9BD5;
            color: white;
        }
        .assessment-table th {
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid #2E75B6;
        }
        .assessment-table td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        .assessment-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        .assessment-table tbody tr:hover {
            background: #f0f0f0;
        }
        .level-be {
            background: #ffebee;
            color: #c62828;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
            display: inline-block;
        }
        .level-ae {
            background: #fff3e0;
            color: #e65100;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
            display: inline-block;
        }
        .level-me {
            background: #e3f2fd;
            color: #1565c0;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
            display: inline-block;
        }
        .level-ee {
            background: #e8f5e9;
            color: #2e7d32;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
            display: inline-block;
        }
        .summary-section {
            background: #e3f2fd;
            border-left: 4px solid #2E75B6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .summary-section h3 {
            color: #2E75B6;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .summary-item label {
            font-weight: bold;
        }
        .hidden-marks {
            display: none;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .report-container {
                max-width: 100%;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .competence-remarks {
            background: #fafafa;
            padding: 10px;
            margin-top: 5px;
            border-radius: 3px;
            font-style: italic;
            color: #555;
            font-size: 11px;
        }
        .grading-scale {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .grading-scale h4 {
            color: #2E75B6;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .grading-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .grading-item {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 3px;
            font-size: 12px;
        }
        .grading-item strong {
            color: #2E75B6;
        }
        .print-button {
            text-align: center;
            margin: 20px 0;
        }
        .print-button button {
            padding: 10px 20px;
            background: #2E75B6;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-button button:hover {
            background: #1e5a96;
        }
    </style>
</head>
<body>

<div class="report-container">

    <!-- REPORT HEADER -->
    <div class="report-header">
        <h1>TEACHING PRACTICE (TP) ASSESSMENT REPORT</h1>
        <div class="report-type"><?= $reportType ?></div>
    </div>

    <!-- STUDENT INFORMATION -->
    <div class="student-info">
        <div class="info-item">
            <span class="info-label">STUDENT REGISTRATION NO</span>
            <span class="info-value"><?= Html::encode($model->student_reg_no) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">SCHOOL</span>
            <span class="info-value"><?= $model->school ? Html::encode($model->school->school_name) : 'N/A' ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">LEARNING AREA</span>
            <span class="info-value"><?= $model->learningArea ? Html::encode($model->learningArea->learning_area_name) : 'N/A' ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">SUPERVISOR</span>
            <span class="info-value"><?= $model->examiner ? Html::encode($model->examiner->name) : 'N/A' ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">ASSESSMENT DATE</span>
            <span class="info-value"><?= \Yii::$app->formatter->asDate($model->assessment_date, 'php:d/m/Y') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">TIME</span>
            <span class="info-value"><?= substr($model->start_time, 0, 5) ?> - <?= substr($model->end_time, 0, 5) ?></span>
        </div>
    </div>

    <!-- GRADING SCALE REFERENCE -->
    <div class="grading-scale">
        <h4>TP E24 GRADING SCALE</h4>
        <div class="grading-grid">
            <div class="grading-item">
                <strong style="color: #c62828;">BE</strong><br>
                Below Expectations<br>
                <small>Score: 0-3</small>
            </div>
            <div class="grading-item">
                <strong style="color: #e65100;">AE</strong><br>
                Approaching Expectations<br>
                <small>Score: 4-5</small>
            </div>
            <div class="grading-item">
                <strong style="color: #1565c0;">ME</strong><br>
                Meets Expectations<br>
                <small>Score: 6-7</small>
            </div>
            <div class="grading-item">
                <strong style="color: #2e7d32;">EE</strong><br>
                Exceeds Expectations<br>
                <small>Score: 8-10</small>
            </div>
        </div>
    </div>

    <!-- COMPETENCE AREAS TABLE -->
    <table class="assessment-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">COMPETENCE AREA</th>
                <?php if ($showMarks): ?>
                    <th style="width: 12%">SCORE</th>
                    <th style="width: 12%">LEVEL</th>
                <?php endif; ?>
                <th style="width: 31%">FEEDBACK / REMARKS</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            foreach ($model->grades as $grade): 
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td>
                        <strong><?= $grade->competenceArea ? Html::encode($grade->competenceArea->competence_name) : 'N/A' ?></strong>
                        <?php if ($grade->competenceArea && $grade->competenceArea->description): ?>
                            <br><small style="color: #666; font-weight: normal;"><?= Html::encode($grade->competenceArea->description) ?></small>
                        <?php endif; ?>
                    </td>
                    <?php if ($showMarks): ?>
                        <td>
                            <?= $grade->score ?>/10
                        </td>
                        <td>
                            <?php
                            $levelClass = 'level-' . strtolower($grade->level);
                            echo '<span class="' . $levelClass . '">' . $grade->level . '</span>';
                            ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php if ($grade->remarks): ?>
                            <div class="competence-remarks">
                                <?= nl2br(Html::encode($grade->remarks)) ?>
                            </div>
                        <?php else: ?>
                            <em style="color: #999;">-</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- SUMMARY SECTION -->
    <?php if ($showMarks): ?>
        <div class="summary-section">
            <h3>ASSESSMENT SUMMARY</h3>
            <div class="summary-item">
                <label>TOTAL SCORE:</label>
                <span><strong><?= $model->total_score ?>/100</strong></span>
            </div>
            <div class="summary-item">
                <label>OVERALL PERFORMANCE LEVEL:</label>
                <span>
                    <?php
                    $levelClass = 'level-' . strtolower($model->overall_level);
                    echo '<span class="' . $levelClass . '">' . $model->overall_level;
                    
                    $ranges = [
                        'BE' => '(0-39)',
                        'AE' => '(40-54)',
                        'ME' => '(55-79)',
                        'EE' => '(80-100)'
                    ];
                    echo ' ' . $ranges[$model->overall_level] . '</span>';
                    ?>
                </span>
            </div>
        </div>
    <?php else: ?>
        <div class="summary-section">
            <h3>FEEDBACK SUMMARY</h3>
            <p>Your supervisor has completed the assessment. Please review the feedback for each competence area above to understand your performance and areas for improvement.</p>
        </div>
    <?php endif; ?>

    <!-- PAGE BREAK FOR PRINTING -->
    <div class="page-break"></div>

    <!-- PRINT BUTTON -->
    <div class="print-button no-print">
        <button onclick="window.print()">Print Report</button>
    </div>

</div>

</body>
</html>
