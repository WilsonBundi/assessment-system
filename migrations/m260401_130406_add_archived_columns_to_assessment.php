<?php

use yii\db\Migration;

class m260401_130406_add_archived_columns_to_assessment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('assessment', 'archived', $this->integer()->defaultValue(0));
        $this->addColumn('assessment', 'archived_at', $this->timestamp()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('assessment', 'archived_at');
        $this->dropColumn('assessment', 'archived');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260401_130406_add_archived_columns_to_assessment cannot be reverted.\n";

        return false;
    }
    */
}
