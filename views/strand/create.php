<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Strand $model */

$this->title = 'Create Strand';
$this->params['breadcrumbs'][] = ['label' => 'Strands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="strand-create">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
