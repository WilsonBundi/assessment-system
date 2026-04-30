<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SchoolClass $model */

$this->title = 'Create School Class';
$this->params['breadcrumbs'][] = ['label' => 'School Classes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="school-class-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
