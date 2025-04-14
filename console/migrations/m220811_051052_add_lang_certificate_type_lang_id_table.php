<?php

use yii\db\Migration;

/**
 * Class m220811_051052_add_lang_certificate_type_lang_id_table
 */
class m220811_051052_add_lang_certificate_type_lang_id_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->addColumn('lang_certificate_type', 'lang_id', $this->integer(11)->after('lang')->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220811_051052_add_lang_certificate_type_lang_id_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220811_051052_add_lang_certificate_type_lang_id_table cannot be reverted.\n";

        return false;
    }
    */
}
