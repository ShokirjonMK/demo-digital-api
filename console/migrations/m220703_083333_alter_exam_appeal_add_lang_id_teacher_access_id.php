<?php

use yii\db\Migration;

/**
 * Class m220703_083333_alter_exam_appeal_add_lang_id_teacher_access_id
 */
class m220703_083333_alter_exam_appeal_add_lang_id_teacher_access_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_appeal` ADD `lang_id` int null COMMENT 'tili ';");
        $this->execute("ALTER TABLE `exam_appeal` ADD `teacher_access_id` int null COMMENT 'tekshiruvchi teacher id';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220703_083333_alter_exam_appeal_add_lang_id_teacher_access_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220703_083333_alter_exam_appeal_add_lang_id_teacher_access_id cannot be reverted.\n";

        return false;
    }
    */
}
