<?php

use yii\db\Migration;

class m260414_081613_add_student_reg_no_to_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260414_081613_add_student_reg_no_to_users cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->addColumn('users', 'student_reg_no', $this->string(50));
        $this->createIndex('idx-users-student_reg_no', 'users', 'student_reg_no');
    }

    public function safeDown()
    {
        $this->dropIndex('idx-users-student_reg_no', 'users');
        $this->dropColumn('users', 'student_reg_no');
        return true;
    }
    */
}
