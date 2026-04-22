<?php

use yii\db\Migration;

class m260414_143000_create_student_supervisor_assignment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('student_supervisor_assignment', [
            'assignment_id' => $this->primaryKey(),
            'student_reg_no' => $this->string(50)->notNull(),
            'supervisor_user_id' => $this->integer()->notNull(),
            'assigned_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-student-supervisor-assignment-student_reg_no',
            'student_supervisor_assignment',
            'student_reg_no',
            true
        );

        $this->addForeignKey(
            'fk-student-supervisor-assignment-supervisor',
            'student_supervisor_assignment',
            'supervisor_user_id',
            'users',
            'user_id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-student-supervisor-assignment-supervisor', 'student_supervisor_assignment');
        $this->dropIndex('idx-student-supervisor-assignment-student_reg_no', 'student_supervisor_assignment');
        $this->dropTable('student_supervisor_assignment');

        return true;
    }
}
