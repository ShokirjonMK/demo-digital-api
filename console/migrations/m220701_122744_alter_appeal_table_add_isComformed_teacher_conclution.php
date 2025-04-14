<?php

use yii\db\Migration;

/**
 * Class m220701_122744_alter_appeal_table_add_isComformed_teacher_conclution
 */
class m220701_122744_alter_appeal_table_add_isComformed_teacher_conclution extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_appeal` ADD `type` int null COMMENT 'qanoatlantirilgani muammo qanday ekanligi ';");
        $this->execute("ALTER TABLE `exam_appeal` ADD `teacher_conclusion` text null COMMENT 'teacher xulosa ';");
        $this->execute("ALTER TABLE `exam_appeal` ADD `conclusion` text null COMMENT 'umumiy xulosa ';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220701_122744_alter_appeal_table_add_isComformed_teacher_conclution cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220701_122744_alter_appeal_table_add_isComformed_teacher_conclution cannot be reverted.\n";

        return false;
    }
    */
}
