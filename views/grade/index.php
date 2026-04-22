<?php

use app\models\Grade;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\components\RbacHelper;

/** @var yii\web\View $this */
/** @var app\models\GradeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Grades';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grade-index index-page">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="index-actions">
        <?php if (RbacHelper::isSupervisor()): ?>
            <?= Html::a('Add New Grade', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <div class="search-form">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'grade_id',
            [
                'attribute' => 'assessment_id',
                'value' => function($model) {
                    return $model->assessment ? "REG: {$model->assessment->student_reg_no}" : 'N/A';
                }
            ],
            [
                'attribute' => 'competence_id',
                'value' => function($model) {
                    return $model->competenceArea ? $model->competenceArea->competence_name : 'N/A';
                }
            ],
            [
                'attribute' => 'score',
                'value' => function($model) {
                    return $model->score ? "{$model->score}/10" : 'N/A';
                }
            ],
            [
                'attribute' => 'level',
                'value' => function($model) {
                    $badges = [
                        'BE' => '<span class="badge bg-danger">BE - Below</span>',
                        'AE' => '<span class="badge bg-warning text-dark">AE - Approaching</span>',
                        'ME' => '<span class="badge bg-info">ME - Meets</span>',
                        'EE' => '<span class="badge bg-success">EE - Exceeds</span>'
                    ];
                    return $badges[$model->level] ?? $model->level;
                },
                'format' => 'html'
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Grade $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'grade_id' => $model->grade_id]);
                 },
                'template' => RbacHelper::isSupervisor() ? '{view} {update} {delete}' : '{view}'
            ],
        ],
    ]); ?>


</div>
