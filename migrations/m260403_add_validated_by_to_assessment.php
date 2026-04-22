<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `assessment` for tracking who validated assessments.
 */
class m260403_add_validated_by_to_assessment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('assessment', 'validated_by', $this->integer()->null()->comment('User ID of zone coordinator who validated this assessment'));
        $this->addColumn('assessment', 'validated_at', $this->dateTime()->null()->comment('Timestamp when assessment was validated'));
        
        // Add foreign key constraint
        $this->addForeignKey(
            'fk_assessment_validated_by',
            'assessment',
            'validated_by',
            'user',
            'user_id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_assessment_validated_by', 'assessment');
        $this->dropColumn('assessment', 'validated_by');
        $this->dropColumn('assessment', 'validated_at');
    }
}
