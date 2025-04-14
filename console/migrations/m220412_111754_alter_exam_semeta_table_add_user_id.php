<?php

use yii\db\Migration;

/**
 * Class m220412_111754_alter_exam_semeta_table_add_user_id
 */
class m220412_111754_alter_exam_semeta_table_add_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_semeta` ADD `type` int default 1 NULL COMMENT 'type 1 - random teshiradi 2 - teacher o`zi tekshiradi id ';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220412_111754_alter_exam_semeta_table_add_user_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220412_111754_alter_exam_semeta_table_add_user_id cannot be reverted.\n";

        return false;
    }
    */
}
