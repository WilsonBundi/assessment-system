<?php

use yii\db\Migration;

class m260428_100000_add_class_id_to_assessment extends Migration
{
    public function safeUp()
    {
        $this->addColumn('assessment', 'class_id', $this->integer()->null()->after('school_id'));

        $this->addForeignKey(
            'fk_assessment_class',
            'assessment',
            'class_id',
            'class',
            'class_id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_assessment_class', 'assessment');
        $this->dropColumn('assessment', 'class_id');
    }
}
