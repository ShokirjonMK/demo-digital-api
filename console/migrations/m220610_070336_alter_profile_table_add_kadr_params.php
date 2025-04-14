<?php

use yii\db\Migration;

/**
 * Class m220610_070336_alter_profile_table_add_kadr_params
 */
class m220610_070336_alter_profile_table_add_kadr_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `profile` ADD `diploma_type_id` int null COMMENT 'diploma_type';");
        $this->execute("ALTER TABLE `profile` ADD `degree_id` int null COMMENT 'darajasi id';");
        $this->execute("ALTER TABLE `profile` ADD `academic_degree_id` int null COMMENT 'academic_degree id';");
        $this->execute("ALTER TABLE `profile` ADD `degree_info_id` int null COMMENT 'degree_info id';");
        $this->execute("ALTER TABLE `profile` ADD `partiya_id` int null COMMENT 'partiya id';");

        // $this->addForeignKey('pdt_profile_diploma_type', 'profile', 'diploma_type_id', 'diploma_type', 'id');
        // $this->addForeignKey('pd_profile_degree_id', 'profile', 'degree_id', 'degree', 'id');
        // $this->addForeignKey('pad_profile_academic_degree_id', 'profile', 'academic_degree_id', 'academic_degree', 'id');
        // $this->addForeignKey('pdi_profile_degree_info_id', 'profile', 'degree_info_id', 'degree_info', 'id');
        // $this->addForeignKey('pp_profile_partiya_id', 'profile', 'partiya_id', 'partiya', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropForeignKey('pdt_profile_diploma_type', 'profile');
        // $this->dropForeignKey('pd_profile_degree_id', 'profile');
        // $this->dropForeignKey('pad_profile_academic_degree_id', 'profile');
        // $this->dropForeignKey('pdi_profile_degree_info_id', 'profile');
        // $this->dropForeignKey('pp_profile_partiya_id', 'profile');
        echo "m220610_070336_alter_profile_table_add_kadr_params cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220610_070336_alter_profile_table_add_kadr_params cannot be reverted.\n";

        return false;
    }
    */
}
