<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_zones}}`.
 */
class m260423_092354_create_user_zones_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_zones}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'zone_id' => $this->integer()->notNull(),
        ]);

        // Create indexes
        $this->createIndex('idx-user_zones-user_id', '{{%user_zones}}', 'user_id');
        $this->createIndex('idx-user_zones-zone_id', '{{%user_zones}}', 'zone_id');
        $this->createIndex('idx-user_zones-unique', '{{%user_zones}}', ['user_id', 'zone_id'], true);

        // Add foreign keys
        $this->addForeignKey('fk-user_zones-user_id', '{{%user_zones}}', 'user_id', '{{%users}}', 'user_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-user_zones-zone_id', '{{%user_zones}}', 'zone_id', '{{%zone}}', 'zone_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys
        $this->dropForeignKey('fk-user_zones-user_id', '{{%user_zones}}');
        $this->dropForeignKey('fk-user_zones-zone_id', '{{%user_zones}}');

        // Drop indexes
        $this->dropIndex('idx-user_zones-user_id', '{{%user_zones}}');
        $this->dropIndex('idx-user_zones-zone_id', '{{%user_zones}}');
        $this->dropIndex('idx-user_zones-unique', '{{%user_zones}}');

        $this->dropTable('{{%user_zones}}');
    }
}
