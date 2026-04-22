<?php

use app\models\Strand;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\StrandSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Strands';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="strand-index index-page">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="index-actions">
        <?= Html::a('Add New Strand', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="search-form">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'strand_id',
            'learning_area_id',
            'name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Strand $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'strand_id' => $model->strand_id]);
                 }
            ],
        ],
    ]); ?>


</div>
