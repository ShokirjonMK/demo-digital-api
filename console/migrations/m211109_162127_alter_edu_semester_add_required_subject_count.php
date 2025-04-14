<?php

use yii\db\Migration;

/**
 * Class m211109_162127_alter_edu_semester_add_required_subject_count
 */
class m211109_162127_alter_edu_semester_add_required_subject_count extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `edu_semestr` add  `required_subject_count` INT(11) NOT NULL default 0;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211109_162127_alter_edu_semester_add_required_subject_count cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211109_162127_alter_edu_semester_add_required_subject_count cannot be reverted.\n";

        return false;
    }
    */
}
