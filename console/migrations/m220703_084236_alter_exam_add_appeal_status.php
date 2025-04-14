<?php

use yii\db\Migration;

/**
 * Class m220703_084236_alter_exam_add_appeal_status
 */
class m220703_084236_alter_exam_add_appeal_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam` ADD `status_appeal` int default(0) COMMENT 'appeal status all';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220703_084236_alter_exam_add_appeal_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220703_084236_alter_exam_add_appeal_status cannot be reverted.\n";

        return false;
    }
    */
}
