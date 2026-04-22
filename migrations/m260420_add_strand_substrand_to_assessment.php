<?php

use yii\db\Migration;

class m260420_add_strand_substrand_to_assessment extends Migration
{
    public function safeUp()
    {
        $this->addColumn('assessment', 'strand_id', $this->integer()->null()->after('learning_area_id'));
        $this->addColumn('assessment', 'substrand_id', $this->integer()->null()->after('strand_id'));
        
        // Add foreign key constraints
        $this->addForeignKey(
            'fk_assessment_strand',
            'assessment',
            'strand_id',
            'strand',
            'strand_id',
            'SET NULL',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk_assessment_substrand',
            'assessment',
            'substrand_id',
            'substrand',
            'substrand_id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_assessment_substrand', 'assessment');
        $this->dropForeignKey('fk_assessment_strand', 'assessment');
        $this->dropColumn('assessment', 'substrand_id');
        $this->dropColumn('assessment', 'strand_id');
    }
}
