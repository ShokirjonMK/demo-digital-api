<?php

use yii\db\Migration;

/**
 * Class m220617_111718_alter_access_table_add_tabel_params
 */
class m220617_111718_alter_access_table_add_tabel_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `user_access` ADD `work_rate_id` int null COMMENT 'work_rate';");
        $this->execute("ALTER TABLE `user_access` ADD `tabel_number` varchar(22) null COMMENT 'tabel_number';");
        $this->execute("ALTER TABLE `user_access` ADD `job_title_id` int null COMMENT 'job_title';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220617_111718_alter_access_table_add_tabel_params cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220617_111718_alter_access_table_add_tabel_params cannot be reverted.\n";

        return false;
    }
    */
}
