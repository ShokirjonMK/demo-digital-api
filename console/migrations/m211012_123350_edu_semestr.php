<?php

use yii\db\Migration;

/**
 * Class m211012_123350_edu_semestr
 */
class m211012_123350_edu_semestr extends Migration
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
        $this->createTable('edu_semestr', [
            'id' => $this->primaryKey(),
            'edu_plan_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->notNull(),
            'semestr_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'start_date' => $this->dateTime(),
            'end_date' => $this->dateTime(),
            'one_credit_sum' => $this->double(),
            'all_credit_sum' => $this->double(),
            'is_checked' => $this->tinyInteger(1)->defaultValue(0),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('pe_edu_semestr_edu_plan_id', 'edu_semestr', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('ce_edu_semestr_course_id', 'edu_semestr', 'course_id', 'course', 'id');
        $this->addForeignKey('se_edu_semestr_semestr_id', 'edu_semestr', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('se_edu_semestr_edu_year_id', 'edu_semestr', 'edu_year_id', 'edu_year', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('pe_edu_semestr_edu_plan_id', 'edu_semestr');
        $this->dropForeignKey('ce_edu_semestr_course_id', 'edu_semestr');
        $this->dropForeignKey('se_edu_semestr_semestr_id', 'edu_semestr');
        $this->dropForeignKey('se_edu_semestr_edu_year_id', 'edu_semestr');
        $this->dropTable('edu_semestr');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_123350_edu_semestr cannot be reverted.\n";

        return false;
    }
    */
}
