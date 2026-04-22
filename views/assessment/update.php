<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Assessment $model */

$this->title = 'Update Assessment: ' . $model->assessment_id;
$this->params['breadcrumbs'][] = ['label' => 'Assessments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->assessment_id, 'url' => ['view', 'assessment_id' => $model->assessment_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="assessment-update">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
