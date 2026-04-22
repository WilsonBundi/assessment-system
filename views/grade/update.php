<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Grade $model */

$this->title = 'Update Grade: ' . $model->grade_id;
$this->params['breadcrumbs'][] = ['label' => 'Grades', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->grade_id, 'url' => ['view', 'grade_id' => $model->grade_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="grade-update">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
