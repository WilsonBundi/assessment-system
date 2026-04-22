<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\CompetenceArea $model */

$this->title = 'Update Competence Area: ' . $model->competence_name;
$this->params['breadcrumbs'][] = ['label' => 'Competence Areas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->competence_name, 'url' => ['view', 'competence_id' => $model->competence_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="competence-area-update">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
