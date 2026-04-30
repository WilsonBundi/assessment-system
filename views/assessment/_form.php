<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Users;
use app\models\School;
use app\models\SchoolClass;
use app\models\LearningArea;
use app\models\Strand;
use app\models\Substrand;
use app\components\AssessmentImageBehavior;
use app\models\CompetenceArea;
use app\models\Grade;

/** @var yii\web\View $this */
/** @var app\models\Assessment $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="assessment-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'student_reg_no')->textInput(['maxlength' => true, 'placeholder' => 'e.g., STU001']) ?>
        </div>
        <div class="col-md-6">
            <?php
            // Only show the current supervisor and the one assigned to the student
            $currentSupervisorId = Yii::$app->user->id;
            $assignedSupervisorId = null;
            if ($model->student_reg_no) {
                $assignment = \app\models\StudentSupervisorAssignment::findOne(['student_reg_no' => $model->student_reg_no]);
                if ($assignment) {
                    $assignedSupervisorId = $assignment->supervisor_user_id;
                }
            }
            $supervisorIds = array_unique(array_filter([$currentSupervisorId, $assignedSupervisorId]));
            $supervisors = \app\models\Users::find()->where(['user_id' => $supervisorIds])->all();
            echo $form->field($model, 'examiner_user_id')->dropDownList(
                ArrayHelper::map($supervisors, 'user_id', 'name'),
                ['prompt' => 'Select Examiner...', 'class' => 'form-control searchable-select', 'data-placeholder' => 'Select Examiner...']
            );
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            // Only show the school assigned to the student (if any), else all schools
            $schoolOptions = [];
            if ($model->student_reg_no) {
                $student = \app\models\Students::findOne(['student_reg_no' => $model->student_reg_no]);
                if ($student && $student->school_id) {
                    $school = \app\models\School::findOne($student->school_id);
                    if ($school) {
                        $schoolOptions = [$school->school_id => $school->school_name];
                    }
                }
            }
            if (empty($schoolOptions)) {
                $schoolOptions = ArrayHelper::map(School::find()->all(), 'school_id', 'school_name');
            }
            echo $form->field($model, 'school_id')->dropDownList(
                $schoolOptions,
                ['prompt' => 'Select School...', 'class' => 'form-control searchable-select', 'data-placeholder' => 'Select School...', 'id' => 'school-select']
            );
            ?>
        </div>
        <div class="col-md-6">
            <?php
            $classOptions = [];
            $selectedSchoolId = $model->school_id;

            if ($model->class_id && !$selectedSchoolId) {
                $selectedClass = SchoolClass::findOne($model->class_id);
                if ($selectedClass) {
                    $selectedSchoolId = $selectedClass->school_id;
                }
            }

            if ($model->student_reg_no) {
                $student = \app\models\Students::findOne(['student_reg_no' => $model->student_reg_no]);
                if ($student && $student->class_id) {
                    $class = SchoolClass::findOne(['class_id' => $student->class_id]);
                    if ($class && (!$selectedSchoolId || $class->school_id == $selectedSchoolId)) {
                        $classOptions = [$class->class_id => $class->class_name];
                        $selectedSchoolId = $class->school_id;
                    }
                }
            }

            if (empty($classOptions) && $selectedSchoolId) {
                $classOptions = ArrayHelper::map(
                    SchoolClass::find()->where(['school_id' => $selectedSchoolId])->all(),
                    'class_id',
                    'class_name'
                );
            }

            echo $form->field($model, 'class_id')->dropDownList(
                $classOptions,
                ['prompt' => 'Select Class...', 'class' => 'form-control searchable-select', 'data-placeholder' => 'Select Class...', 'id' => 'class-select']
            );
            ?>
            <?php if ($model->school_id && empty($classOptions)): ?>
                <div class="mt-2" style="color: #000;">
                    <small>There are currently no classes available for the selected school. Please ask TP Office to add classes for this school.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'learning_area_id')->dropDownList(
                ArrayHelper::map(LearningArea::find()->all(), 'learning_area_id', 'learning_area_name'),
                ['prompt' => 'Select Learning Area...', 'class' => 'form-select', 'id' => 'learning-area-select']
            ) ?>
        </div>
    </div>

    <div id="strand-feedback" class="alert alert-warning d-none" role="alert" style="margin-top: 15px;"></div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'strand_id')->dropDownList(
                $model->learning_area_id ? ArrayHelper::map(
                    Strand::find()->where(['learning_area_id' => $model->learning_area_id])->all(),
                    'strand_id',
                    'name'
                ) : [],
                ['prompt' => 'Select Strand...', 'class' => 'form-control searchable-select', 'data-placeholder' => 'Select Strand...', 'id' => 'strand-select']
            ) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'substrand_id')->dropDownList(
                $model->strand_id ? ArrayHelper::map(
                    Substrand::find()->where(['strand_id' => $model->strand_id])->all(),
                    'substrand_id',
                    'name'
                ) : [],
                ['prompt' => 'Select Substrand...', 'class' => 'form-control searchable-select', 'data-placeholder' => 'Select Substrand...', 'id' => 'substrand-select']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'assessment_date')->input('date') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'start_time')->input('time') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'end_time')->input('time') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'total_score')->textInput(['readonly' => true]) 
                ->hint('Auto-computed from competence area grades (after saving)') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'overall_level')->textInput(['readonly' => true])
                ->hint('Auto-computed: BE (0-39) | AE (40-54) | ME (55-79) | EE (80-100)') ?>
        </div>
    </div>

    <?php if (!$model->isNewRecord && !\app\components\RbacHelper::isTpOffice()): ?>
        <hr>
        <h5>Assessment Images <span class="badge bg-info">Max 5 images</span></h5>
        
        <div class="mb-3">
            <?= Html::label('Upload Images') ?>
            <?= Html::fileInput('images[]', null, [
                'type' => 'file',
                'multiple' => true,
                'accept' => 'image/jpeg,image/png,image/gif,image/webp',
                'class' => 'form-control',
                'id' => 'image-upload'
            ]) ?>
            <small class="text-muted">Supported formats: JPG, PNG, GIF, WebP. Max 5 images, 5MB each.</small>
        </div>
    <?php elseif (!$model->isNewRecord && \app\components\RbacHelper::isTpOffice()): ?>
        <hr>
        <div class="alert alert-warning">TP Office users are not permitted to upload evidence images.</div>
    <?php endif; ?>

    <?php if (!$model->isNewRecord): ?>
        <div id="existing-images">
            <?php
            $images = AssessmentImageBehavior::getImages($model->assessment_id);
            if (count($images) > 0): ?> 
                <h6>Existing Images (<?= count($images) ?>/<?= AssessmentImageBehavior::MAX_IMAGES ?>)</h6>
                <div class="image-gallery" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-bottom: 15px;">
                    <?php foreach ($images as $image): ?>
                        <div class="image-item" style="border: 1px solid #ddd; padding: 5px; border-radius: 5px; text-align: center;">
                            <img src="<?= AssessmentImageBehavior::getImageUrl($model->assessment_id, $image) ?>" 
                                 alt="Assessment image" 
                                 style="max-width: 100%; max-height: 120px; margin-bottom: 5px;">
                            <small><?= $image ?></small><br>
                            <?= Html::a('Delete', ['delete-image', 'assessment_id' => $model->assessment_id, 'filename' => $image], [
                                'class' => 'btn btn-sm btn-danger',
                                'data' => ['confirm' => 'Delete this image?', 'method' => 'post']
                            ]) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <hr>
        <?php $competenceAreas = $competenceAreas ?? \app\models\CompetenceArea::find()->orderBy(['competence_id' => SORT_ASC])->all(); ?>
        <h5>Grade Assessment <span class="badge bg-success"><?= count($competenceAreas) ?> Competence Areas</span></h5>
        
        <div class="alert alert-info">
            <h6>TP E24 Grading Scale</h6>
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

        <?php
        // Get all competence areas - must have exactly 10
        $competenceAreas = CompetenceArea::find()->orderBy('competence_id')->all();
        
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
        ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover grading-grid">
                <thead class="table-light">
                    <tr>
                        <th style="width: 3%; text-align: center;">
                            <input type="checkbox" id="select-all-competence" title="Select/deselect all competencies" />
                        </th>
                        <th style="width: 2%">#</th>
                        <th style="width: 33%">Competence Area (<?= count($competenceAreas) ?> Official Standards)</th>
                        <th style="width: 12%">Score (0-10)</th>
                        <th style="width: 18%">Level</th>
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
                                       name="grades[<?= $competence->competence_id ?>][score]" 
                                       class="form-control score-input" 
                                       min="0" max="10" 
                                       value="<?= $grade ? $grade['score'] : '' ?>"
                                       data-competence-id="<?= $competence->competence_id ?>"
                                       placeholder="0-10">
                                <?php if ($gradeId): ?>
                                    <input type="hidden" name="grades[<?= $competence->competence_id ?>][grade_id]" value="<?= $gradeId ?>">
                                <?php endif; ?>
                            </td>
                            <td>
                                <select name="grades[<?= $competence->competence_id ?>][level]" 
                                        class="form-control level-select" 
                                        data-competence-id="<?= $competence->competence_id ?>">
                                    <option value="">Select Level</option>
                                    <option value="BE" <?= $grade && $grade['level'] === 'BE' ? 'selected' : '' ?>>BE (0-3)</option>
                                    <option value="AE" <?= $grade && $grade['level'] === 'AE' ? 'selected' : '' ?>>AE (4-5)</option>
                                    <option value="ME" <?= $grade && $grade['level'] === 'ME' ? 'selected' : '' ?>>ME (6-7)</option>
                                    <option value="EE" <?= $grade && $grade['level'] === 'EE' ? 'selected' : '' ?>>EE (8-10)</option>
                                </select>
                            </td>
                            <td>
                                <textarea name="grades[<?= $competence->competence_id ?>][remarks]" 
                                          class="form-control remarks-textarea" 
                                          rows="2" 
                                          data-competence-id="<?= $competence->competence_id ?>"
                                          placeholder="Enter remarks..."><?= $grade ? Html::encode($grade['remarks']) : '' ?></textarea>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mb-3">
            <button type="button" id="save-grades-btn" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Grades
            </button>
            <small class="text-muted">Grades are saved automatically when you update the assessment</small>
        </div>
    <?php endif; ?>

    <div class="alert alert-info">
        <strong>How to use:</strong>
        <ol>
            <li>Fill in assessment details and save</li>
            <li>Upload images (optional - max 5 images)</li>
            <li>Grade each of the 12 competence areas below</li>
            <li>The total score and overall level will be auto-calculated</li>
        </ol>
    </div>

    <div class="btn-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create Assessment' : 'Update Assessment', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php
    // Register JavaScript for dynamic dropdown filtering
    $getClassesUrl = Url::to(['get-classes']);
    $getStudentDetailsUrl = Url::to(['get-student-details']);
    $getStrandsUrl = Url::to(['get-strands']);
    $getSubstrandsUrl = Url::to(['get-substrands']);
    $js = <<<JS
    $(document).ready(function() {
        function populateClasses(schoolId, selectedClassId) {
            $('#class-select').empty().append('<option value="">Select Class...</option>');
            if (!schoolId) {
                return;
            }
            $.ajax({
                url: '$getClassesUrl',
                type: 'GET',
                data: {school_id: schoolId},
                dataType: 'json',
                success: function(response) {
                    if (response.classes && Object.keys(response.classes).length > 0) {
                        $.each(response.classes, function(classId, className) {
                            $('#class-select').append($('<option></option>')
                                .attr('value', classId)
                                .text(className));
                        });
                        if (selectedClassId) {
                            $('#class-select').val(selectedClassId);
                        }
                    }
                }
            });
        }

        // Handle Student selection/change - load student school and class
        $('#assessment-student_reg_no').on('change blur', function() {
            var studentRegNo = $(this).val().trim();
            if (!studentRegNo) {
                return;
            }

            $.ajax({
                url: '$getStudentDetailsUrl',
                type: 'GET',
                data: {student_reg_no: studentRegNo},
                dataType: 'json',
                success: function(response) {
                    if (response.student && response.student.school_id) {
                        $('#school-select').val(response.student.school_id);
                        populateClasses(response.student.school_id, response.student.class_id);
                    }
                }
            });
        });

        // Handle School change - load Classes
        $('#learning-area-select').on('change', function() {
            var learningAreaId = $(this).val();

            // Reset Strand and Substrand dropdowns
            $('#strand-select').empty().append(new Option('Select Strand...', '', false, false)).trigger('change');
            $('#substrand-select').empty().append(new Option('Select Substrand...', '', false, false)).trigger('change');
            setAssessmentFeedback('');

            if (learningAreaId) {
                $.ajax({
                    url: '$getStrandsUrl',
                    type: 'GET',
                    data: {learning_area_id: learningAreaId},
                    dataType: 'json',
                    success: function(response) {
                        if (response.strands && Object.keys(response.strands).length > 0) {
                            $.each(response.strands, function(strandId, strandName) {
                                $('#strand-select').append(new Option(strandName, strandId, false, false));
                            });
                            $('#strand-select').trigger('change');
                        } else {
                            setAssessmentFeedback('No strands are defined for the selected learning area. Please ask TP Office to create strands before continuing.');
                        }
                    },
                    error: function() {
                        setAssessmentFeedback('Unable to load strands right now. Please try again or contact support.');
                    }
                });
            }
        });

        // Handle Strand change - load Substrands
        $('#strand-select').on('change', function() {
            var strandId = $(this).val();

            $('#substrand-select').empty().append(new Option('Select Substrand...', '', false, false)).trigger('change');
            setAssessmentFeedback('');

            if (strandId) {
                $.ajax({
                    url: '$getSubstrandsUrl',
                    type: 'GET',
                    data: {strand_id: strandId},
                    dataType: 'json',
                    success: function(response) {
                        if (response.substrands && Object.keys(response.substrands).length > 0) {
                            $.each(response.substrands, function(substrandId, substrandName) {
                                $('#substrand-select').append(new Option(substrandName, substrandId, false, false));
                            });
                            $('#substrand-select').trigger('change');
                        } else {
                            setAssessmentFeedback('No substrands are defined for the selected strand. Please ask TP Office to create substrands before continuing.');
                        }
                    },
                    error: function() {
                        setAssessmentFeedback('Unable to load substrands right now. Please try again or contact support.');
                    }
                });
            }
        });
    });
    JS;
    $this->registerJs($js);
    ?>

</div>
</div>

<?php if (!$model->isNewRecord): ?>
<?php
$this->registerJs("
    var assessmentId = " . $model->assessment_id . ";
    var stateKey = 'assessment-grading-state-' + assessmentId;

    function getOverallLevel(total) {
        if (total >= 80) return 'EE';
        if (total >= 55) return 'ME';
        if (total >= 40) return 'AE';
        return 'BE';
    }

    function updateTotals() {
        var total = 0;
        $('.score-input').each(function() {
            var val = parseInt($(this).val());
            if (!isNaN(val)) {
                total += val;
            }
        });

        var overall = getOverallLevel(total);
        $('input[name=\"Assessment[total_score]\"]').val(total);
        $('input[name=\"Assessment[overall_level]\"]').val(overall);
        return total;
    }

    function persistState() {
        var state = {};

        $('.competence-row').each(function() {
            var competenceId = $(this).data('competence-id');
            var score = $(this).find('.score-input').val();
            var level = $(this).find('.level-select').val();
            var checked = $(this).find('.rubric-checkbox').is(':checked');

            state[competenceId] = {score: score, level: level, checked: checked};
        });

        localStorage.setItem(stateKey, JSON.stringify(state));
    }

    function loadState() {
        var raw = localStorage.getItem(stateKey);
        if (!raw) return;

        try {
            var state = JSON.parse(raw);
            $.each(state, function(competenceId, values) {
                var row = $('.competence-row[data-competence-id=\"' + competenceId + '\"]');
                if (!row.length) return;

                if (values.score !== undefined) {
                    row.find('.score-input').val(values.score);
                }
                if (values.level !== undefined) {
                    row.find('.level-select').val(values.level);
                }
                row.find('.rubric-checkbox').prop('checked', values.checked);

                if (values.checked) {
                    row.css('background-color', '#f0f8f0');
                } else {
                    row.css('background-color', '');
                }
            });
            updateTotals();
        } catch (e) {
            console.warn('Unable to load grade state', e);
        }
    }

    // Auto-calculate level based on score
    $(document).on('input', '.score-input', function() {
        var score = parseInt($(this).val());
        var competenceId = $(this).data('competence-id');
        var levelSelect = $('.level-select[data-competence-id=\"' + competenceId + '\"]');

        if (!isNaN(score)) {
            if (score >= 0 && score <= 3) {
                levelSelect.val('BE');
            } else if (score >= 4 && score <= 5) {
                levelSelect.val('AE');
            } else if (score >= 6 && score <= 7) {
                levelSelect.val('ME');
            } else if (score >= 8 && score <= 10) {
                levelSelect.val('EE');
            }
        }
        updateTotals();
        persistState();
    });

    // If row checkbox is used, auto-fill default score/level, then trigger update
    $(document).on('change', '.rubric-checkbox', function() {
        var row = $(this).closest('.competence-row');
        var scoreInput = row.find('.score-input');
        var levelSelect = row.find('.level-select');

        if ($(this).is(':checked')) {
            if (!scoreInput.val()) scoreInput.val(5);
            if (!levelSelect.val()) levelSelect.val('AE');
            row.css('background-color', '#f0f8f0');
        } else {
            scoreInput.val('');
            levelSelect.val('');
            row.css('background-color', '');
        }

        scoreInput.trigger('change');
        levelSelect.trigger('change');
        updateTotals();
        persistState();
    });

    // Mark row as completed when both score and level are filled
    $(document).on('change', '.score-input, .level-select', function() {
        var competenceId = $(this).closest('.competence-row').data('competence-id');
        var row = $('.competence-row[data-competence-id=\"' + competenceId + '\"]');
        var scoreInput = row.find('.score-input');
        var levelSelect = row.find('.level-select');
        var checkbox = row.find('.rubric-checkbox');

        if (scoreInput.val() && levelSelect.val()) {
            row.css('background-color', '#f0f8f0');
            checkbox.prop('checked', true);
        } else {
            row.css('background-color', '');
            checkbox.prop('checked', false);
        }

        updateTotals();
        persistState();
    });

    //Â Select/Deselect all competence rows
    $('#select-all-competence').on('change', function() {
        var checked = $(this).is(':checked');
        $('.rubric-checkbox').prop('checked', checked);

        $('.competence-row').each(function() {
            var row = $(this);
            var scoreInput = row.find('.score-input');
            var levelSelect = row.find('.level-select');

            if (checked) {
                if (!scoreInput.val()) scoreInput.val(5);
                if (!levelSelect.val()) levelSelect.val('AE');
                row.css('background-color', '#f0f8f0');
            } else {
                scoreInput.val('');
                levelSelect.val('');
                row.css('background-color', '');
            }

            row.find('.score-input').trigger('change');
            row.find('.level-select').trigger('change');
        });

        updateTotals();
        persistState();
    });

    // Load persisted state
    loadState();

    // Save grades via AJAX
    $('#save-grades-btn').on('click', function() {
        var formData = new FormData();
        formData.append('_csrf', $('meta[name=\"csrf-token\"]').attr('content'));

        $('input[name^=\"grades\"], select[name^=\"grades\"], textarea[name^=\"grades\"]').each(function() {
            formData.append($(this).attr('name'), $(this).val());
        });

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['save-grid']) ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    localStorage.removeItem(stateKey);
                    alert('Grades saved successfully!');
                    location.reload();
                } else {
                    alert('Error saving grades: ' + response.message);
                }
            },
            error: function() {
                alert('Error saving grades. Please try again.');
            }
        });
    });
");
?>
<?php endif; ?>

