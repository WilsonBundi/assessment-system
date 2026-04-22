<?php

use yii\db\Migration;

class m260401_125702_add_archive_columns_to_assessment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('assessment', 'archived', $this->integer()->defaultValue(0)->comment('Archive status: 0=active, 1=archived'));
        $this->addColumn('assessment', 'archived_at', $this->timestamp()->null()->comment('Timestamp when record was archived'));
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
        echo "m260401_125702_add_archive_columns_to_assessment cannot be reverted.\n";

        return false;
    }
    */
}
