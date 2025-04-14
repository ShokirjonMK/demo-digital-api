<?php

use yii\db\Migration;

/**
 * Class m220127_074102_alter_subject_sillabus_add_topic_count
 */
class m220127_074102_alter_subject_sillabus_add_topic_count extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `subject_sillabus` ADD `topic_count` INT(11) NULL default 0 COMMENT 'mavzu soni ' ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220127_074102_alter_subject_sillabus_add_topic_count cannot be reverted.\n";

        return false;
    }

}
