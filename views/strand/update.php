<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Strand $model */

$this->title = 'Update Strand: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Strands', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'strand_id' => $model->strand_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="strand-update">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
