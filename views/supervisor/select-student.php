<?php
use yii\helpers\Html;

$this->title = 'Select Student for Assessment';
$this->params['breadcrumbs'][] = ['label' => 'Supervisor', 'url' => ['/supervisor/profile']];
$this->params['breadcrumbs'][] = 'Create Assessment';
?>

<div class="supervisor-select-student">
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                
                <!-- Step Indicator -->
                <div class="alert alert-info mb-4" role="alert">
                    <strong>Step 1 of 5:</strong> Select Student for Assessment
                </div>

                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-search"></i> Search Students</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?= \yii\helpers\Url::to(['/supervisor/select-student']) ?>" class="form-inline mb-3">
                            <div class="form-group flex-grow-1 me-2">
                                <input 
                                    type="text" 
                                    name="search" 
                                    class="form-control form-control-lg w-100" 
                                    placeholder="Enter student registration number..." 
                                    value="<?= Html::encode($searchQuery) ?>"
                                    required
                                >
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <?php if (!empty($searchQuery)): ?>
                                <a href="<?= \yii\helpers\Url::to(['/supervisor/select-student']) ?>" class="btn btn-secondary btn-lg ms-2">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            <?php endif; ?>
                        </form>
                        <p class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Only students assigned to you by TP Office are available for assessment. Search by registration number to find your assigned students.
                        </p>
                    </div>
                </div>

                <!-- Students List -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-list"></i> Available Students</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($students)): ?>
                            <div class="list-group">
                                <?php foreach ($students as $index => $student): ?>
                                    <form method="POST" class="mb-2">
                                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                        <input type="hidden" name="student_reg_no" value="<?= Html::encode($student) ?>">
                                        <button type="submit" class="list-group-item list-group-item-action text-start">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-user-graduate"></i> 
                                                        <?= Html::encode($student) ?>
                                                    </h6>
                                                    <p class="text-muted mb-0">
                                                        Registration Number
                                                    </p>
                                                </div>
                                                <span class="badge bg-primary rounded-pill">Select</span>
                                            </div>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (!empty($searchQuery)): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> 
                                No students found matching "<strong><?= Html::encode($searchQuery) ?></strong>".
                                <div class="mt-2">
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="collapse" data-bs-target="#newStudentForm">
                                        Create Assessment for This Student
                                    </button>
                                </div>
                                <div class="collapse mt-2" id="newStudentForm">
                                    <form method="POST" class="card card-body">
                                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                        <input type="hidden" name="student_reg_no" value="<?= Html::encode($searchQuery) ?>">
                                        <p class="text-muted mb-3">
                                            You are about to create an assessment for: 
                                            <strong><?= Html::encode($searchQuery) ?></strong>
                                        </p>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-plus-circle"></i> Create Assessment
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i> 
                                You currently have no students assigned. Please ask TP Office to assign students before creating assessments.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-4">
                    <?= Html::a(
                        '<i class="fas fa-arrow-left"></i> Back to Profile',
                        ['/supervisor/profile'],
                        ['class' => 'btn btn-secondary']
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .supervisor-select-student .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border-radius: 0.5rem;
    }

    .supervisor-select-student .card-header {
        padding: 1rem;
        font-weight: 600;
    }

    .supervisor-select-student .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
        transition: all 0.3s ease;
    }

    .supervisor-select-student .btn-primary {
        background-color: #5B9BD5;
        border-color: #5B9BD5;
    }

    .supervisor-select-student .btn-primary:hover {
        background-color: #2E75B6;
        border-color: #2E75B6;
    }

    .supervisor-select-student .badge {
        background-color: #5B9BD5 !important;
    }

    .form-control-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
        font-weight: 500;
    }
</style>
