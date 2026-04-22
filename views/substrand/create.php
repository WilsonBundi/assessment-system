<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Substrand $model */

$this->title = 'Create Substrand';
$this->params['breadcrumbs'][] = ['label' => 'Substrands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="substrand-create">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
