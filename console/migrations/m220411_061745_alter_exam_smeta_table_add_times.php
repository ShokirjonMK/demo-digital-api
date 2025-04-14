<?php

use yii\db\Migration;

/**
 * Class m220411_061745_alter_exam_smeta_table_add_times
 */
class m220411_061745_alter_exam_smeta_table_add_times extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_semeta` ADD `start` int  NULL  AFTER `id`;");
        $this->execute("ALTER TABLE `exam_semeta` ADD `finish` int  NULL  AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220411_061745_alter_exam_smeta_table_add_times cannot be reverted.\n";

        return false;
    }
}
