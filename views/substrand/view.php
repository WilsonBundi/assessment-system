<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Substrand $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Substrands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="substrand-view">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="detail-page-actions">
        <?= Html::a('Update', ['update', 'substrand_id' => $model->substrand_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'substrand_id' => $model->substrand_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'detail-view'],
        'model' => $model,
        'attributes' => [
            'substrand_id',
            'strand_id',
            'name',
        ],
    ]) ?>

</div>
