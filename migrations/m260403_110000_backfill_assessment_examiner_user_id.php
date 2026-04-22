<?php

use yii\db\Migration;

class m260403_110000_backfill_assessment_examiner_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $supervisorId = Yii::$app->db->createCommand(
            'SELECT user_id FROM users WHERE role_id = :role LIMIT 1',
            [':role' => 1]
        )->queryScalar();

        if ($supervisorId) {
            $updated = Yii::$app->db->createCommand(
                'UPDATE assessment SET examiner_user_id = :supervisor WHERE examiner_user_id IS NULL',
                [':supervisor' => $supervisorId]
            )->execute();

            echo "Updated {$updated} assessment(s) with missing examiner_user_id to supervisor ID {$supervisorId}.\n";
        } else {
            echo "No supervisor user found to backfill examiner_user_id. Skipping update.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // We cannot reliably revert examiner_user_id assignments, so we leave this intentionally blank.
        echo "m260403_110000_backfill_assessment_examiner_user_id cannot be reverted.\n";

        return true;
    }
}
