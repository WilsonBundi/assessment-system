<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Grades';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="grade-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Grade', ['/grade/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

			[
				'attribute' => 'level',
				'label' => 'Grade',
			],

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return ['/grade/' . $action, 'grade_id' => $model->grade_id];
                },
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        $label = app\models\Grade::getLevelLabel($model->level);
                        return Html::a('View', $url, ['class' => 'btn btn-link p-0 m-0', 'title' => 'View ' . Html::encode($label)]);
                    },
                    'update' => function ($url, $model, $key) {
                        $label = app\models\Grade::getLevelLabel($model->level);
                        return Html::a('Edit', $url, ['class' => 'btn btn-link p-0 m-0', 'title' => 'Edit ' . Html::encode($label)]);
                    },
                    'delete' => function ($url, $model, $key) {
                        $label = app\models\Grade::getLevelLabel($model->level);
                        return Html::a('Delete', $url, ['class' => 'btn btn-link text-danger p-0 m-0', 'title' => 'Delete ' . Html::encode($label), 'data-confirm' => 'Are you sure you want to delete ' . Html::encode($label) . '?', 'data-method' => 'post']);
                    },
                ],
            ],
        ],
    ]); ?>
</div>