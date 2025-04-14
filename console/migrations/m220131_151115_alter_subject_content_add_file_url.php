<?php

use yii\db\Migration;

/**
 * Class m220131_151115_alter_subject_content_add_file_url
 */
class m220131_151115_alter_subject_content_add_file_url extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `subject_content` ADD `file_url` VARCHAR (255) NULL default NULL COMMENT 'file nomini saqlaymiz' ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220131_151115_alter_subject_content_add_file_url cannot be reverted.\n";

        return false;
    }


}
