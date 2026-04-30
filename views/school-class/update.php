<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SchoolClass $model */

$this->title = 'Update School Class: ' . $model->class_id;
$this->params['breadcrumbs'][] = ['label' => 'School Classes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->class_id, 'url' => ['view', 'class_id' => $model->class_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="school-class-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
