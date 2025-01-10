<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_plan}}`.
 */
class m230506_102500_create_edu_plan_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'edu_plan';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('edu_plan');
        }

        $this->createTable('{{%edu_plan}}', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger(1)->notNull()->comment('type qoshiladi kuzgi qabul(1)  qishgi qabul (2)'),
            'edu_year_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'edu_type_id' => $this->integer()->notNull(),
            'edu_form_id' => $this->integer()->notNull()->comment('ta-lim shakli'),
            'course' => $this->integer()->notNull()->comment('nech kurs o\'qishi'),
            'first_start' => $this->date()->notNull(),
            'first_end' => $this->date()->notNull(),
            'second_start' => $this->date()->notNull(),
            'second_end' => $this->date()->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_edu_plan_table_edu_year_table', 'edu_plan', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_edu_plan_table_faculty_table', 'edu_plan', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_edu_plan_table_direction_table', 'edu_plan', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_edu_plan_table_edu_type_table', 'edu_plan', 'edu_type_id', 'edu_type', 'id');
        $this->addForeignKey('mk_edu_plan_table_edu_form_table', 'edu_plan', 'edu_form_id', 'edu_form', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_edu_plan_table_edu_year_table', 'edu_plan');
        $this->dropForeignKey('mk_edu_plan_table_faculty_table', 'edu_plan');
        $this->dropForeignKey('mk_edu_plan_table_direction_table', 'edu_plan');
        $this->dropForeignKey('mk_edu_plan_table_edu_type_table', 'edu_plan');
        $this->dropForeignKey('mk_edu_plan_table_edu_form_table', 'edu_plan');
        $this->dropTable('{{%edu_plan}}');
    }
}
