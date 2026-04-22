<?php

namespace app\controllers;

use Yii;
use app\models\StudentSupervisorAssignment;
use app\models\Students;
use app\models\Users;
use yii\web\Controller;
use yii\filters\AccessControl;

class AssessedStudentsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $assignments = StudentSupervisorAssignment::find()
            ->where(['supervisor_user_id' => $user->user_id, 'status' => 'assessed'])
            ->with(['student', 'supervisor'])
            ->orderBy(['assigned_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'assignments' => $assignments,
        ]);
    }
}
