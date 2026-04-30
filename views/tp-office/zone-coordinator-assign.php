
<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use Yii;

/* @var $this yii\web\View */
/* @var $zones array */
/* @var $allCoordinators array */
/* @var $zoneCoordinators array */

$this->title = 'Assign Zone Coordinators';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$zoneOptions = ArrayHelper::map($zones, 'zone_id', 'zone_name');
$coordinatorOptions = ArrayHelper::map($zoneCoordinators, 'user_id', 'name');
?>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= Yii::$app->session->getFlash('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= Yii::$app->session->getFlash('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="zone-coordinator-assign-container">
    <!-- Search and Filter Form -->
    <div class="row mb-3">
        <div class="col-12">
            <form method="get" action="" class="d-flex flex-wrap gap-2 align-items-end">
                <div class="form-group mb-0 me-2">
                    <label for="zoneFilter" class="form-label fw-bold">Filter by Zone</label>
                    <?= Html::dropDownList('zone_id', Yii::$app->request->get('zone_id'), $zoneOptions, [
                        'id' => 'zoneFilter',
                        'class' => 'form-select searchable-select',
                        'data-placeholder' => '-- All Zones --',
                        'onchange' => 'this.form.submit();'
                    ]) ?>
                </div>
                <div class="form-group mb-0 me-2">
                    <label for="searchCoordinator" class="form-label fw-bold">Search Coordinator</label>
                    <input type="text" name="search" id="searchCoordinator" class="form-control" placeholder="Enter coordinator name..." value="<?= Html::encode(Yii::$app->request->get('search', '')) ?>">
                </div>
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-shield me-2"></i>Assign Zone Coordinators
                        </h4>
                    </div>
                    <div>
                        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Zone</th>
                                    <th>Assigned Coordinators</th>
                                    <th>Add Coordinator</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($zones as $zone): ?>
                                    <?php
                                        // Get coordinators assigned to this zone
                                        $assignedCoordinators = [];
                                        foreach ($allCoordinators as $coordinator) {
                                            // Check if coordinator is assigned to this zone via user_zones table
                                            $assignment = \app\models\UserZones::findOne(['user_id' => $coordinator->user_id, 'zone_id' => $zone->zone_id]);
                                            if ($assignment) {
                                                $assignedCoordinators[] = $coordinator;
                                            }
                                        }
                                    ?>
                                    <tr<?= count($assignedCoordinators) > 0 ? ' class="table-success"' : '' ?>>
                                        <td>
                                            <strong><?= Html::encode($zone->zone_name) ?></strong>
                                            <?php if (count($assignedCoordinators) > 0): ?>
                                                <br><small class="text-muted"><?= count($assignedCoordinators) ?> coordinator(s)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (count($assignedCoordinators) > 0): ?>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <?php foreach ($assignedCoordinators as $coordinator): ?>
                                                        <span class="badge bg-primary">
                                                            <?= Html::encode($coordinator->name) ?>
                                                            <form method="post" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/remove-zone-coordinator'])) ?>" class="d-inline ms-1" onsubmit="return confirm('Remove this coordinator from the zone?')">
                                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                                <input type="hidden" name="user_id" value="<?= $coordinator->user_id ?>">
                                                                <input type="hidden" name="zone_id" value="<?= $zone->zone_id ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-light border-0 p-0" title="Remove coordinator">
                                                                    <i class="fas fa-times text-white"></i>
                                                                </button>
                                                            </form>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">None Assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="post" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/update-zone-coordinator'])) ?>" class="d-inline assign-coordinator-form">
                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                <?= Html::hiddenInput('zone_id', $zone->zone_id) ?>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <?= Html::dropDownList('coordinator_user_id', null, $coordinatorOptions, [
                                                        'class' => 'form-select form-select-sm coordinator-select searchable-select',
                                                        'data-placeholder' => 'Select coordinator to add',
                                                    ]) ?>
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-plus me-1"></i>Add
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <?php if (count($assignedCoordinators) > 0): ?>
                                                <form method="post" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['tp-office/clear-zone-coordinators'])) ?>" class="d-inline" onsubmit="return confirm('Remove all coordinators from this zone?')">
                                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                    <input type="hidden" name="zone_id" value="<?= $zone->zone_id ?>">
                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-trash me-1"></i>Clear All
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
// Initialize Select2 for searchable dropdowns
$(document).ready(function() {
    $('.searchable-select').select2({
        placeholder: function() {
            return $(this).data('placeholder') || 'Search and select...';
        },
        allowClear: true,
        width: '100%'
    });
});

// Confirmation for remove actions
document.addEventListener('DOMContentLoaded', function() {
    // The confirmation is already in the onsubmit attribute
});
</script>
</div>
