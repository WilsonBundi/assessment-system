<?php

use app\models\LearningArea;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\LearningAreaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Learning Areas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="learning-area-index index-page">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="index-actions">
        <?= Html::a('Add New Learning Area', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="search-form">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'learning_area_id',
            'learning_area_name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LearningArea $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'learning_area_id' => $model->learning_area_id]);
                 }
            ],
        ],
    ]); ?>


</div>
