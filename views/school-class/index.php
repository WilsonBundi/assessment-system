<?php

use app\models\SchoolClass;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\SchoolClassSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'School Classes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="school-class-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create School Class', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'class_id',
            'school_id',
            'class_name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SchoolClass $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'class_id' => $model->class_id]);
                 }
            ],
        ],
    ]); ?>


</div>
