<?php

use yii\db\Migration;

/**
 * Class m230109_123348_alter_subject_sillabus_table
 */
class m230109_123348_alter_subject_sillabus_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('subject_sillabus', 'auditory_time', $this->double()->null()->after('id'));
        $this->addColumn('edu_semestr_subject', 'auditory_time', $this->double()->null()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230109_123348_alter_subject_sillabus_table cannot be reverted.\n";

        return false;
    }
}
