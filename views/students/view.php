<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Students $model */

$this->title = $model->getName() . ' (' . $model->student_reg_no . ')';
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="students-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'student_id' => $model->student_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'student_id' => $model->student_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'student_id',
            'student_reg_no',
            'surname',
            'other_name',
            [
                'attribute' => 'phone_no',
                'label' => 'Phone Number',
                'value' => $model->phone_no ?: 'Not provided',
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
                'value' => $model->email ?: 'Not provided',
            ],
            [
                'attribute' => 'school_id',
                'label' => 'School',
                'value' => $model->school ? $model->school->school_name : 'Not assigned',
            ],
        ],
    ]) ?>

</div>
