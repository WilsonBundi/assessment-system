<?php

use yii\db\Migration;

class m260402_114038_add_zone_school_to_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'zone_id', $this->integer());
        $this->addColumn('users', 'school_id', $this->integer());
        
        // Add foreign key constraints
        $this->addForeignKey('fk_users_zone', 'users', 'zone_id', 'zone', 'zone_id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_users_school', 'users', 'school_id', 'school', 'school_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_users_school', 'users');
        $this->dropForeignKey('fk_users_zone', 'users');
        
        $this->dropColumn('users', 'school_id');
        $this->dropColumn('users', 'zone_id');
        
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260402_114038_add_zone_school_to_users cannot be reverted.\n";

        return false;
    }
    */
}
