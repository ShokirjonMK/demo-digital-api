<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%time_option}}`.
 */
class m220914_105854_create_time_option_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'time_option';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('time_option');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%time_option}}', [
            'id' => $this->primaryKey(),

            'key' => $this->string(1)->notNull(),
            'faculty_id' => $this->integer()->notNull(),
            'edu_plan_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'edu_semester_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'capacity' => $this->integer()->notNull(),
            'type' => $this->integer()->null(),
            'description' => $this->text()->null(),
            'selected' => $this->integer()->notNull()->defaultValue(0),

            'archived' => $this->tinyInteger(1)->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->integer()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('time_option_faculty_id', 'time_option', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('time_option_edu_plan_id', 'time_option', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('time_option_edu_year_id', 'time_option', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('time_option_edu_semester_id', 'time_option', 'edu_semester_id', 'edu_semestr', 'id');
        $this->addForeignKey('time_option_language_id', 'time_option', 'language_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('time_option_faculty_id', 'time_option');
        $this->dropForeignKey('time_option_edu_plan_id', 'time_option');
        $this->dropForeignKey('time_option_edu_year_id', 'time_option');
        $this->dropForeignKey('time_option_edu_semester_id', 'time_option');
        $this->dropForeignKey('time_option_language_id', 'time_option');

        $this->dropTable('{{%time_option}}');
    }
}
