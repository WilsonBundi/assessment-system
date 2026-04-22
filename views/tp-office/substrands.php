<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Sub-Strands';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="substrand-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sub-Strand', ['/substrand/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'substrand_name',
                'label' => 'Sub-Strand',
            ],
            [
                'attribute' => 'strand_id',
                'label' => 'Strand',
                'value' => function ($model) {
                    return $model->strand->strand_name ?? 'N/A';
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return ['/substrand/' . $action, 'substrand_id' => $model->substrand_id];
                },
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('View', $url, ['class' => 'btn btn-link p-0 m-0', 'title' => 'View ' . Html::encode($model->substrand_name)]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('Edit', $url, ['class' => 'btn btn-link p-0 m-0', 'title' => 'Edit ' . Html::encode($model->substrand_name)]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('Delete', $url, ['class' => 'btn btn-link text-danger p-0 m-0', 'title' => 'Delete ' . Html::encode($model->substrand_name), 'data-confirm' => 'Are you sure you want to delete ' . Html::encode($model->substrand_name) . '?', 'data-method' => 'post']);
                    },
                ],
            ],
        ],
    ]); ?>
</div>