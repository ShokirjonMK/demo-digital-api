<?php

use yii\db\Migration;

/**
 * Class m220312_094024_alter_subject_topic_add_subject_category_id
 */
class m220312_094024_alter_subject_topic_add_subject_category_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `subject_topic` ADD `subject_category_id` int NULL COMMENT 'fan turlari boyicha topic uchun';");

        $this->addForeignKey('stsc_subject_topic_subject_category_id', 'subject_topic', 'subject_category_id', 'subject_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('stsc_subject_topic_subject_category_id', 'subject_topic');
        echo "m220312_094024_alter_subject_topic_add_subject_category_id cannot be reverted.\n";

        return false;
    }
}
