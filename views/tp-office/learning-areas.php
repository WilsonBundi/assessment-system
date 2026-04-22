<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Learning Areas';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="learning-area-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Learning Area', ['/learning-area/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'learning_area_name',
                'label' => 'Learning Area',
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return ['/learning-area/' . $action, 'learning_area_id' => $model->learning_area_id];
                },
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('View', $url, ['class' => 'btn btn-link p-0 m-0', 'title' => 'View ' . Html::encode($model->learning_area_name)]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('Edit', $url, ['class' => 'btn btn-link p-0 m-0', 'title' => 'Edit ' . Html::encode($model->learning_area_name)]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('Delete', $url, ['class' => 'btn btn-link text-danger p-0 m-0', 'title' => 'Delete ' . Html::encode($model->learning_area_name), 'data-confirm' => 'Are you sure you want to delete ' . Html::encode($model->learning_area_name) . '?', 'data-method' => 'post']);
                    },
                ],
            ],
        ],
    ]); ?>
</div>