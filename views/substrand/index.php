<?php

use app\models\Substrand;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\SubstrandSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Substrands';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="substrand-index index-page">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="index-actions">
        <?= Html::a('Add New Substrand', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="search-form">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'substrand_id',
            'strand_id',
            'name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Substrand $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'substrand_id' => $model->substrand_id]);
                 }
            ],
        ],
    ]); ?>


</div>
