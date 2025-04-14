<?php

use yii\db\Migration;

/**
 * Class m220806_095204_alter_subject_content_table_add_teacher_access_id
 */
class m220806_095204_alter_subject_content_table_add_teacher_access_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        //  subject_content

        $this->addColumn('subject_content', 'teacher_access_id', $this->integer(1)->after('id'));

        $this->addForeignKey('scts_subject_content_teacher_access_id', 'subject_content', 'teacher_access_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220806_095204_alter_subject_content_table_add_teacher_access_id cannot be reverted.\n";

        $this->dropForeignKey('scts_subject_content_teacher_access_id', 'subject_content');
        return false;
    }
}
