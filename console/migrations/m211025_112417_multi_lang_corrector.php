<?php

use yii\db\Migration;

/**
 * Class m211025_112417_multi_lang_corrector
 */
class m211025_112417_multi_lang_corrector extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `building` DROP `name`;");
        $this->execute("ALTER TABLE `room` DROP `name`;");
        $this->execute("ALTER TABLE `direction` DROP `name`;");
        $this->execute("ALTER TABLE `faculty` DROP `name`;");
        $this->execute("ALTER TABLE `kafedra` DROP `name`;");
        $this->execute("ALTER TABLE `edu_type` DROP `name`;");
        $this->execute("ALTER TABLE `subject` DROP `name`;");
        $this->execute("ALTER TABLE `subject_type` DROP `name`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211025_112417_multi_lang_corrector cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211025_112417_multi_lang_corrector cannot be reverted.\n";

        return false;
    }
    */
}
