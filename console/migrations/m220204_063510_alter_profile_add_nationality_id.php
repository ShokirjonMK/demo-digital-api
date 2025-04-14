<?php

use yii\db\Migration;

/**
 * Class m220204_063510_alter_profile_add_nationality_id
 */
class m220204_063510_alter_profile_add_nationality_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `profile` ADD `nationality_id` int NULL COMMENT 'millati id ';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220204_063510_alter_profile_add_nationality_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220204_063510_alter_profile_add_nationality_id cannot be reverted.\n";

        return false;
    }
    */
}
