<?php

use yii\helpers\Html;
use app\models\CompetenceArea;
use app\models\Grade;

/** @var yii\web\View $this */
/** @var app\models\Assessment $model */

$this->title = "Grade Assessment - " . $model->student_reg_no;
$this->params['breadcrumbs'][] = ['label' => 'Assessments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Get all competence areas (dynamic count)
$competenceAreas = CompetenceArea::find()->orderBy('competence_id')->all();
if (count($competenceAreas) === 0) {
    throw new \yii\web\BadRequestHttpException('System error: No competence areas found. Please configure competence areas before grading.');
}

// Get existing grades for this assessment
$existingGrades = [];
foreach ($model->grades as $grade) {
    $existingGrades[$grade->competence_id] = [
        'grade_id' => $grade->grade_id,
        'score' => $grade->score,
        'level' => $grade->level,
        'remarks' => $grade->remarks
    ];
}

// Grading scale reference
$gradingScale = Grade::getGradingScale();
?>

<div class="assessment-grading-grid">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="text-muted">
            Student: <strong><?= Html::encode($model->student_reg_no) ?></strong> |
            School: <strong><?= $model->school ? Html::encode($model->school->school_name) : 'N/A' ?></strong> |
            Date: <strong><?= $model->assessment_date ?></strong>
        </p>
    </div>

    <div class="alert alert-warning">
        <strong>Required fields:</strong> Score and Level for all listed competence areas must be completed before saving. Missing fields are highlighted in red.
    </div>

    <div class="alert alert-info">
        <h5>TP E24 Grading Scale</h5>
        <div class="row">
            <div class="col-md-3">
                <strong>BE</strong> (Below Expectations)<br>
                <small>Score: 0-3</small>
            </div>
            <div class="col-md-3">
                <strong>AE</strong> (Approaching Expectations)<br>
                <small>Score: 4-5</small>
            </div>
            <div class="col-md-3">
                <strong>ME</strong> (Meets Expectations)<br>
                <small>Score: 6-7</small>
            </div>
            <div class="col-md-3">
                <strong>EE</strong> (Exceeds Expectations)<br>
                <small>Score: 8-10</small>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover grading-grid">
            <thead class="table-light">
                <tr>
                    <th style="width: 3%"><input type="checkbox" id="selectAllRubrics" title="Select/deselect all standards"></th>
                    <th style="width: 2%">#</th>
                    <th style="width: 33%">Competence Area (<?= count($competenceAreas) ?> Official Standards)</th>
                    <th style="width: 12%">Score (0-10) <span class="text-danger">*</span></th>
                    <th style="width: 18%">Level <span class="text-danger">*</span></th>
                    <th style="width: 32%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($competenceAreas as $competence): ?>
                    <?php
                    $grade = $existingGrades[$competence->competence_id] ?? null;
                    $gradeId = $grade ? $grade['grade_id'] : null;
                    $isCompleted = $grade && $grade['score'] !== null && $grade['level'] !== null;
                    ?>
                    <tr class="competence-row" data-competence-id="<?= $competence->competence_id ?>" data-grade-id="<?= $gradeId ?>" style="<?= $isCompleted ? 'background-color: #f0f8f0;' : '' ?>">
                        <td style="text-align: center;">
                            <input type="checkbox" 
                                   class="form-check-input rubric-checkbox" 
                                   data-competence-id="<?= $competence->competence_id ?>"
                                   <?= $isCompleted ? 'checked' : '' ?>"
                                   title="Mark as completed">
                        </td>
                        <td><?= $i++ ?></td>
                        <td>
                            <strong><?= Html::encode($competence->competence_name) ?></strong>
                            <br>
                            <small class="text-muted"><?= Html::encode($competence->description) ?></small>
                        </td>
                        <td>
                            <input type="number" 
                                   class="form-control score-input" 
                                   min="0" 
                                   max="10" 
                                   value="<?= $grade ? $grade['score'] : '' ?>"
                                   placeholder="0-10"
                                   data-competence-id="<?= $competence->competence_id ?>"
                                   required>
                        </td>
                        <td>
                            <select class="form-control level-input" data-competence-id="<?= $competence->competence_id ?>" required>
                                <option value="">Select Level...</option>
                                <option value="BE" <?= ($grade && $grade['level'] === 'BE') ? 'selected' : '' ?>>BE - Below (0-3)</option>
                                <option value="AE" <?= ($grade && $grade['level'] === 'AE') ? 'selected' : '' ?>>AE - Approaching (4-5)</option>
                                <option value="ME" <?= ($grade && $grade['level'] === 'ME') ? 'selected' : '' ?>>ME - Meets (6-7)</option>
                                <option value="EE" <?= ($grade && $grade['level'] === 'EE') ? 'selected' : '' ?>>EE - Exceeds (8-10)</option>
                            </select>
                        </td>
                        <td>
                            <textarea class="form-control remarks-input" 
                                      rows="2" 
                                      placeholder="Additional remarks..."
                                      data-competence-id="<?= $competence->competence_id ?>"><?= $grade ? Html::encode($grade['remarks']) : '' ?></textarea>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> <strong>Progress:</strong> 
                <span id="completedCount">0</span> of <span id="totalCount"><?= count($competenceAreas) ?></span> standards completed
                <div class="progress mt-2" style="height: 20px;">
                    <div id="progressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Summary</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Total Score:</strong> <span id="totalScore">0</span>/100</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Overall Level:</strong> <span id="overallLevel" class="badge bg-secondary">-</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="btn-group mt-4">
        <button type="button" id="saveButton" class="btn btn-success" disabled>
            <i class="fas fa-save"></i> Save All Grades
        </button>
        <?= Html::a('Back to Assessment', ['view', 'assessment_id' => $model->assessment_id], ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

</div>

<style>
.grading-grid tbody tr:hover {
    background-color: #f5f5f5;
}

.grading-grid .score-input,
.grading-grid .level-input {
    font-size: 0.95rem;
}

.grading-grid .remarks-input {
    font-size: 0.9rem;
    resize: vertical;
}

#totalScore {
    font-size: 1.2rem;
    font-weight: bold;
    color: #007bff;
}

#overallLevel {
    font-size: 1.1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const assessmentId = <?= $model->assessment_id ?>;
    const scoreInputs = document.querySelectorAll('.score-input');
    const levelInputs = document.querySelectorAll('.level-input');
    const saveButton = document.getElementById('saveButton');
    const rubricCheckboxes = document.querySelectorAll('.rubric-checkbox');
    const totalCount = document.getElementById('totalCount');
    const completedCount = document.getElementById('completedCount');
    const progressBar = document.getElementById('progressBar');

    // STRICT: Ensure we have correct number of competence areas
    const expectedCount = scoreInputs.length;
    if (scoreInputs.length === 0) {
        alert('ERROR: No competence areas loaded. Cannot grade.');
        document.body.style.opacity = '0.5';
        saveButton.disabled = true;
    }

    totalCount.textContent = expectedCount;

    // Update summary on input change
    function updateSummary() {
        let totalScore = 0;
        let completed = 0;
        
        scoreInputs.forEach(input => {
            const score = parseInt(input.value) || 0;
            const competenceId = input.dataset.competenceId;
            const levelSelect = document.querySelector(`.level-input[data-competence-id="${competenceId}"]`);
            const row = document.querySelector(`tr[data-competence-id="${competenceId}"]`);
            const checkbox = document.querySelector(`.rubric-checkbox[data-competence-id="${competenceId}"]`);
            
            totalScore += score;
            
            if (input.value !== '' && levelSelect && levelSelect.value !== '') {
                completed++;
                if (row) row.style.backgroundColor = '#f0f8f0';
                if (checkbox) checkbox.checked = true;
            } else {
                if (row) row.style.backgroundColor = '';
                if (checkbox) checkbox.checked = false;
            }
        });

        // Enable save button ONLY when all required competencies are completed
        if (completed === expectedCount) {
            saveButton.disabled = false;
            saveButton.classList.remove('btn-secondary');
            saveButton.classList.add('btn-success');
        } else {
            saveButton.disabled = true;
            saveButton.classList.add('btn-secondary');
            saveButton.classList.remove('btn-success');
        }

        document.getElementById('totalScore').textContent = totalScore;
        completedCount.textContent = completed;
        const percentage = (completed / expectedCount) * 100;
        progressBar.style.width = percentage + '%';

        // Classify overall level
        let levelBadge = 'BE';
        let levelBg = 'bg-danger';
        if (totalScore >= 80) {
            levelBadge = 'EE';
            levelBg = 'bg-success';
        } else if (totalScore >= 55) {
            levelBadge = 'ME';
            levelBg = 'bg-info';
        } else if (totalScore >= 40) {
            levelBadge = 'AE';
            levelBg = 'bg-warning text-dark';
        }

        const levelElement = document.getElementById('overallLevel');
        levelElement.textContent = levelBadge;
        levelElement.className = 'badge ' + levelBg;
    }

    // Auto-populate level based on score
    scoreInputs.forEach(scoreInput => {
        scoreInput.addEventListener('input', function() {
            const competenceId = this.dataset.competenceId;
            const score = parseInt(this.value);
            const levelSelect = document.querySelector(`.level-input[data-competence-id="${competenceId}"]`);

            if (!levelSelect) return;

            if (!isNaN(score)) {
                if (score >= 8) {
                    levelSelect.value = 'EE';
                } else if (score >= 6) {
                    levelSelect.value = 'ME';
                } else if (score >= 4) {
                    levelSelect.value = 'AE';
                } else if (score >= 0) {
                    levelSelect.value = 'BE';
                }
            }

            // remove error style when user types
            const row = document.querySelector(`tr[data-competence-id="${competenceId}"]`);
            if (row) {
                row.querySelector('td:nth-child(4)').style.border = '';
                row.querySelector('td:nth-child(5)').style.border = '';
                row.style.backgroundColor = '#f0f8f0';
            }

            updateSummary();
        });
    });

    // Update summary when level changes
    levelInputs.forEach(levelInput => {
        levelInput.addEventListener('change', updateSummary);
    });

    // Handle checkbox clicks - Auto-grade when checked
    rubricCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const competenceId = this.dataset.competenceId;
            const row = document.querySelector(`tr[data-competence-id="${competenceId}"]`);
            const scoreInput = row ? row.querySelector('.score-input') : null;
            const levelInput = row ? row.querySelector('.level-input') : null;

            if (this.checked) {
                // Auto-fill score if empty
                if (!scoreInput.value) {
                    scoreInput.value = '7'; // Default to ME (Meets Expectations)
                }
                
                // Auto-populate level based on the score
                const score = parseInt(scoreInput.value) || 0;
                if (score >= 8) {
                    levelInput.value = 'EE';
                } else if (score >= 6) {
                    levelInput.value = 'ME';
                } else if (score >= 4) {
                    levelInput.value = 'AE';
                } else {
                    levelInput.value = 'BE';
                }
                
                scoreInput.focus();
            }

            updateSummary();
        });
    });

    // Save all grades
    saveButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        console.log('Save button clicked');

        // Validate correct number of competence rows
        const allRows = document.querySelectorAll('.competence-row');
        if (allRows.length !== expectedCount) {
            alert(`ERROR: System expects ${expectedCount} competence areas but found ${allRows.length}. Please refresh the page.`);
            return;
        }

        // Validate all required fields are filled
        let isValid = true;
        let errorCount = 0;
        let missingFields = [];
        
        allRows.forEach((row, index) => {
            const scoreInput = row.querySelector('.score-input');
            const levelInput = row.querySelector('.level-input');
            const competenceId = row.dataset.competenceId;
            const competenceName = row.querySelector('strong').textContent;
            
            if (scoreInput.value === '' || !levelInput.value) {
                isValid = false;
                errorCount++;
                missingFields.push(`#${index + 1} ${competenceName}`);
                row.style.backgroundColor = '#ffc9c9'; // Highlight incomplete rows
                row.querySelector('td:nth-child(4)').style.border = '2px solid #dc3545';
                row.querySelector('td:nth-child(5)').style.border = '2px solid #dc3545';
            } else {
                row.style.backgroundColor = '#f0f8f0';
                row.querySelector('td:nth-child(4)').style.border = '';
                row.querySelector('td:nth-child(5)').style.border = '';
            }
        });

        if (!isValid) {
            const errorMessage = `ERROR: Please complete all fields. You have ${expectedCount - errorCount}/${expectedCount} standards completed.\n\nMissing fields in:\n${missingFields.join('\n')}`;
            alert(errorMessage);
            return;
        }

        const grades = [];
        document.querySelectorAll('.competence-row').forEach(row => {
            const competenceId = row.dataset.competenceId;
            const gradeId = row.dataset.gradeId;
            const scoreInput = row.querySelector('.score-input');
            const levelInput = row.querySelector('.level-input');
            const remarksInput = row.querySelector('.remarks-input');

            if (scoreInput.value !== '' && levelInput.value) {
                grades.push({
                    gradeId: gradeId,
                    competenceId: competenceId,
                    assessmentId: assessmentId,
                    score: scoreInput.value,
                    level: levelInput.value,
                    remarks: remarksInput.value
                });
            }
        });

        // Disable save button during submission
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Send to server
        fetch('<?= \yii\helpers\Url::toRoute(['assessment/save-grid']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({ grades: grades, assessmentId: assessmentId })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert(`Successfully saved all ${expectedCount} competence areas!`);
                window.location.href = '<?= \yii\helpers\Url::toRoute(['assessment/view', 'assessment_id' => '']) ?>' + assessmentId;
            } else {
                alert('Error saving grades: ' + (data.message || 'Unknown error'));
                saveButton.disabled = false;
                saveButton.innerHTML = '<i class="fas fa-save"></i> Save All Grades';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving grades: ' + error.message);
            saveButton.disabled = false;
            saveButton.innerHTML = '<i class="fas fa-save"></i> Save All Grades';
        });
    });

    // Initial summary calculation
    updateSummary();
});
</script>
