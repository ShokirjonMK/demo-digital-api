<?php

use yii\db\Migration;

/**
 * Class m211027_135455_deleted_name
 */
class m211027_135455_deleted_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `semestr` DROP `name`;");
        $this->execute("ALTER TABLE `course` DROP `name`;");
        $this->execute("ALTER TABLE `para` DROP `name`;");
        $this->execute("ALTER TABLE `subject_category` DROP `name`;");
        $this->execute("ALTER TABLE `exams_type` DROP `name`;");
        $this->execute("ALTER TABLE `edu_year` DROP `name`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211027_135455_deleted_name cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211027_135455_deleted_name cannot be reverted.\n";

        return false;
    }
    */
}
