<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Schools';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="school-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create School', ['/school/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'school_name',
                'label' => 'School',
            ],
            [
                'attribute' => 'school_address',
                'label' => 'Address',
            ],
            [
                'attribute' => 'zone_id',
                'label' => 'Zone',
                'value' => function ($model) {
                    return $model->zone->zone_name ?? 'N/A';
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return ['/school/' . $action, 'school_id' => $model->school_id];
                },
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('View', $url, ['class' => 'btn btn-link p-0 m-0', 'title' => 'View ' . Html::encode($model->school_name)]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('Edit', $url, ['class' => 'btn btn-link p-0 m-0', 'title' => 'Edit ' . Html::encode($model->school_name)]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('Delete', $url, ['class' => 'btn btn-link text-danger p-0 m-0', 'title' => 'Delete ' . Html::encode($model->school_name), 'data-confirm' => 'Are you sure you want to delete ' . Html::encode($model->school_name) . '?', 'data-method' => 'post']);
                    },
                ],
            ],
        ],
    ]); ?>
</div>