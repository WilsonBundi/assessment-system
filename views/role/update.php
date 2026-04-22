<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Role $model */

$this->title = 'Update Role: ' . $model->role_name;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->role_name, 'url' => ['view', 'role_id' => $model->role_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="role-update">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="form-page">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
