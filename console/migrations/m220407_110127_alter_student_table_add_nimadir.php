<?php

use yii\db\Migration;

/**
 * Class m220407_110127_alter_student_table_add_nimadir
 */
class m220407_110127_alter_student_table_add_nimadir extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `student` ADD `partners_count` int NULL COMMENT 'partners_count  ';");
        $this->execute("ALTER TABLE `student` ADD `live_location` text NULL COMMENT 'live_location  ';");
        $this->execute("ALTER TABLE `student` ADD `parent_phone` varchar(55) NULL COMMENT 'parent_phone  ';");
        $this->execute("ALTER TABLE `student` ADD `res_person_phone` varchar(55) NULL COMMENT 'res_person_phone  ';");
        $this->execute("ALTER TABLE `student` ADD `last_education` text NULL COMMENT 'last_education  ';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220407_110127_alter_student_table_add_nimadir cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_110127_alter_student_table_add_nimadir cannot be reverted.\n";

        return false;
    }
    */
}
