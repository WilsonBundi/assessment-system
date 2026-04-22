<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Zone */

$this->title = 'Update Zone: ' . $model->zone_name;
$this->params['breadcrumbs'][] = ['label' => 'Zones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->zone_name, 'url' => ['view', 'id' => $model->zone_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="zone-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>