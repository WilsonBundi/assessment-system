<?php

use app\models\Zone;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ZoneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Zones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zone-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Zone', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'zone_id',
            'zone_name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>