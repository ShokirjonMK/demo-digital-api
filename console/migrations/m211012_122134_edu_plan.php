<?php

use yii\db\Migration;

/**
 * Class m211012_122134_edu_plan
 */
class m211012_122134_edu_plan extends Migration
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
        $this->createTable('edu_plan', [
            'id' => $this->primaryKey(),
            'course' => $this->integer()->notNull(),
            'semestr' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'edu_type_id' => $this->integer()->notNull(),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('ep_edu_plan_edu_year_id', 'edu_plan', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('fp_edu_plan_faculty_id', 'edu_plan', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('dp_edu_plan_direction_id', 'edu_plan', 'direction_id', 'direction', 'id');
        $this->addForeignKey('tp_edu_plan_edu_type_id', 'edu_plan', 'edu_type_id', 'edu_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ep_edu_plan_edu_year_id', 'edu_plan');
        $this->dropForeignKey('fp_edu_plan_faculty_id', 'edu_plan');
        $this->dropForeignKey('dp_edu_plan_direction_id', 'edu_plan');
        $this->dropForeignKey('tp_edu_plan_edu_type_id', 'edu_plan');
        $this->dropTable('edu_plan');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_122134_edu_plan cannot be reverted.\n";

        return false;
    }
    */
}
