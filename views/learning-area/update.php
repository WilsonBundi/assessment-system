<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\LearningArea $model */

$this->title = 'Update Learning Area: ' . $model->learning_area_name;
$this->params['breadcrumbs'][] = ['label' => 'Learning Areas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->learning_area_name, 'url' => ['view', 'learning_area_id' => $model->learning_area_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="learning-area-update">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
