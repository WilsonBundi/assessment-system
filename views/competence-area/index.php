<?php

use app\models\CompetenceArea;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\CompetenceAreaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Competence Areas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competence-area-index index-page">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="index-actions">
        <?= Html::a('Add New Competence Area', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="search-form">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'competence_id',
            'competence_name',
            'description:ntext',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, CompetenceArea $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'competence_id' => $model->competence_id]);
                 }
            ],
        ],
    ]); ?>


</div>
