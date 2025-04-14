<?php

use yii\db\Migration;

/**
 * Class m211215_143924_turuncate
 */
class m211215_143924_turuncate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        // $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        // $this->execute("TRUNCATE table  `edu_semestr_subject`;");
        // $this->execute("TRUNCATE table  `exam`;");
        // $this->execute("TRUNCATE table  `edu_semestr_exams_type`;");
        // $this->execute("TRUNCATE table  `time_table`;");
        // $this->execute("TRUNCATE table  `student_time_table`;");
        // $this->execute("SET FOREIGN_KEY_CHECKS = 1;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211215_143924_turuncate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211215_143924_turuncate cannot be reverted.\n";

        return false;
    }
    */
}
