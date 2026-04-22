<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\School $model */

$this->title = 'Update School: ' . $model->school_name;
$this->params['breadcrumbs'][] = ['label' => 'Schools', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->school_name, 'url' => ['view', 'school_id' => $model->school_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="school-update">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>

</div>
