<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\LearningArea $model */

$this->title = 'Create Learning Area';
$this->params['breadcrumbs'][] = ['label' => 'Learning Areas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="learning-area-create">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
