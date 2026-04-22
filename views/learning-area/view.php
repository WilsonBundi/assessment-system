<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\LearningArea $model */

$this->title = $model->learning_area_name;
$this->params['breadcrumbs'][] = ['label' => 'Learning Areas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="learning-area-view">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="detail-page-actions">
        <?= Html::a('Update', ['update', 'learning_area_id' => $model->learning_area_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'learning_area_id' => $model->learning_area_id], [
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
            'learning_area_id',
            'learning_area_name',
        ],
    ]) ?>

</div>
