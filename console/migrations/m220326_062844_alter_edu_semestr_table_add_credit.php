<?php

use yii\db\Migration;

/**
 * Class m220326_062844_alter_edu_semestr_table_add_credit
 */
class m220326_062844_alter_edu_semestr_table_add_credit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `edu_semestr` ADD `credit` double  default(0)  AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220326_062844_alter_edu_semestr_table_add_credit cannot be reverted.\n";

        return false;
    }
}
