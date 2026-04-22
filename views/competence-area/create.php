<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\CompetenceArea $model */

$this->title = 'Create Competence Area';
$this->params['breadcrumbs'][] = ['label' => 'Competence Areas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competence-area-create">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
