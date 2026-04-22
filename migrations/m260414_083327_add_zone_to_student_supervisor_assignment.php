<?php

use yii\db\Migration;

class m260414_083327_add_zone_to_student_supervisor_assignment extends Migration
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
        echo "m260414_083327_add_zone_to_student_supervisor_assignment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->addColumn('student_supervisor_assignment', 'zone_id', $this->integer());
        $this->addForeignKey('fk_assignment_zone', 'student_supervisor_assignment', 'zone_id', 'zone', 'zone_id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_assignment_zone', 'student_supervisor_assignment');
        $this->dropColumn('student_supervisor_assignment', 'zone_id');
        return true;
    }
    */
}
