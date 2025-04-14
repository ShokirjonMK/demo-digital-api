<?php

use yii\db\Migration;

/**
 * Class m211108_154746_semestr_add_type
 */
class m211104_055718_semestr_add_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `semestr` add  `type` INT(11) NOT NULL default(1) COMMENT 'kuz=1 bahorhi = 2' ; ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211104_055718_semestr_add_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211108_154746_semestr_add_type cannot be reverted.\n";

        return false;
    }
    */
}
