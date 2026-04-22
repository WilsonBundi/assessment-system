<?php

use app\models\Users;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UsersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index index-page">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="index-actions">
        <?= Html::a('Add New Users', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="search-form">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'user_id',
            [
                'attribute' => 'role_id',
                'label' => 'Role',
                'value' => function(Users $model) {
                    return $model->role ? $model->role->role_name : 'Unknown';
                }
            ],
            'username',
            'password',
            'payroll_no',
            [
                'label' => 'Allowed Actions',
                'format' => 'raw',
                'value' => function(Users $model) {
                    $roleName = $model->role ? $model->role->role_name : null;
                    $actions = app\components\RbacHelper::getActionsForRole($roleName);
                    return implode('<br>', $actions);
                }
            ],
            //'name',
            //'phone',
            //'status',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Users $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'user_id' => $model->user_id]);
                 }
            ],
        ],
    ]); ?>


</div>
