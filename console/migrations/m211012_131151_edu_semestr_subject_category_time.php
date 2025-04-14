<?php

use yii\db\Migration;

/**
 * Class m211012_131151_edu_semestr_subject_category_time
 */
class m211012_131151_edu_semestr_subject_category_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('edu_semestr_subject_category_time', [
            'id' => $this->primaryKey(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->notNull(),
            'hours' => $this->integer()->notNull(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('se_edu_semestr_subject_category_time_edu_semestr_edu_s_s_id', 'edu_semestr_subject_category_time', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('se_edu_semestr_subject_category_time_edu_semestr_s_c_id', 'edu_semestr_subject_category_time', 'subject_category_id', 'subject_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('se_edu_semestr_subject_category_time_edu_semestr_edu_s_s_id', 'edu_semestr_subject_category_time');
        $this->dropForeignKey('se_edu_semestr_subject_category_time_edu_semestr_s_c_id', 'edu_semestr_subject_category_time');
        $this->dropTable('edu_semestr_subject_category_time');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_131151_edu_semestr_subject_category_time cannot be reverted.\n";

        return false;
    }
    */
}
