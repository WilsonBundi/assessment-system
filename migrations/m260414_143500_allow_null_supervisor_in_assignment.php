<?php

use yii\db\Migration;

class m260414_143500_allow_null_supervisor_in_assignment extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey('fk_student_supervisor_supervisor', 'student_supervisor_assignment');
        $this->alterColumn('student_supervisor_assignment', 'supervisor_user_id', $this->integer()->null());
        $this->addForeignKey(
            'fk_student_supervisor_supervisor',
            'student_supervisor_assignment',
            'supervisor_user_id',
            'users',
            'user_id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-student-supervisor-assignment-supervisor', 'student_supervisor_assignment');
        $this->alterColumn('student_supervisor_assignment', 'supervisor_user_id', $this->integer()->notNull());
        $this->addForeignKey(
            'fk-student-supervisor-assignment-supervisor',
            'student_supervisor_assignment',
            'supervisor_user_id',
            'users',
            'user_id',
            'CASCADE',
            'CASCADE'
        );

        return true;
    }
}
