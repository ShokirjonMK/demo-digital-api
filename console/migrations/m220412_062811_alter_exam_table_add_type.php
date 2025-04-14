<?php

use yii\db\Migration;

/**
 * Class m220412_062811_alter_exam_table_add_type
 */
class m220412_062811_alter_exam_table_add_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam` ADD `type` int default 1 NULL COMMENT 'type 1 - random teshiradi 2 - teacher o`zi tekshiradi id ';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220412_062811_alter_exam_table_add_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220412_062811_alter_exam_table_add_type cannot be reverted.\n";

        return false;
    }
    */
}
