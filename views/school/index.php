<?php

use app\models\School;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\SchoolSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Schools';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="school-index index-page">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="index-actions">
        <?= Html::a('+ Add New School', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php if (!empty($searchModel)): ?>
        <?= $this->render('_search', ['model' => $searchModel]) ?>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'grid-view'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['class' => 'serial-column']],

            'school_code',
            'school_name',
            [
                'attribute' => 'zone_id',
                'label' => 'Zone',
                'value' => function ($model) {
                    return $model->zone ? $model->zone->zone_name : $model->zone_id;
                },
                'filter' => false,
            ],
            [
                'class' => ActionColumn::className(),
                'headerOptions' => ['class' => 'action-column'],
                'contentOptions' => ['class' => 'action-column'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('View', $url, ['class' => 'btn-view']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('Edit', $url, ['class' => 'btn-update']);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('Delete', $url, ['class' => 'btn-delete', 'data-confirm' => 'Are you sure?']);
                    },
                ],
                'urlCreator' => function ($action, School $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'school_id' => $model->school_id]);
                }
            ],
        ],
    ]); ?>

</div>
