<?php

use yii\db\Migration;

/**
 * Class m220127_072122_alter_subject_table_add_smester_id
 */
class m220127_072122_alter_subject_table_add_smester_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `subject` ADD `semestr_id` INT(11) NULL COMMENT 'fanga smester' ;");
        $this->addForeignKey('ss_subject_smester_id_mk', 'subject', 'semestr_id', 'semestr', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ss_subject_smester_id_mk', 'subject');
        echo "m220127_072122_alter_subject_table_add_smester_id cannot be reverted.\n";

        return false;
    }

}
