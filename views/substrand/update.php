<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Substrand $model */

$this->title = 'Update Substrand: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Substrands', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'substrand_id' => $model->substrand_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="substrand-update">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
