<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\School $model */

$this->title = $model->school_name;
$this->params['breadcrumbs'][] = ['label' => 'Schools', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="school-view">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="detail-page-actions">
        <?= Html::a('Edit', ['update', 'school_id' => $model->school_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'school_id' => $model->school_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this school?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="detail-view">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'school_id',
                'school_code',
                'school_name',
                [
                    'attribute' => 'zone_id',
                    'label' => 'Zone',
                    'value' => function ($model) {
                        return $model->zone ? $model->zone->zone_name : $model->zone_id;
                    },
                ],
            ],
        ]) ?>
    </div>

</div>
