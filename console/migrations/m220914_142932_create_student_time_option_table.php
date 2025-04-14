<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_student_time_option}}`.
 */
class m220914_142932_create_student_time_option_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_time_option';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_time_option');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%student_time_option}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'time_option_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'edu_semester_id' => $this->integer()->null(),
            'language_id' => $this->integer()->null(),


            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->integer()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('student_time_option_faculty_id', 'student_time_option', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('student_time_option_edu_plan_id', 'student_time_option', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('student_time_option_edu_year_id', 'student_time_option', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('student_time_option_edu_semester_id', 'student_time_option', 'edu_semester_id', 'edu_semestr', 'id');
        $this->addForeignKey('student_time_option_language_id', 'student_time_option', 'language_id', 'languages', 'id');
        $this->addForeignKey('student_time_option_student_id', 'student_time_option', 'student_id', 'student', 'id');
        $this->addForeignKey('student_time_option_user_id', 'student_time_option', 'user_id', 'users', 'id');
        $this->addForeignKey('student_time_option_time_option_id', 'student_time_option', 'time_option_id', 'time_option', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('student_time_option_faculty_id', 'student_time_option');
        $this->dropForeignKey('student_time_option_edu_plan_id', 'student_time_option');
        $this->dropForeignKey('student_time_option_edu_year_id', 'student_time_option');
        $this->dropForeignKey('student_time_option_edu_semester_id', 'student_time_option');
        $this->dropForeignKey('student_time_option_language_id', 'student_time_option');
        $this->dropForeignKey('student_time_option_student_id', 'student_time_option');
        $this->dropForeignKey('student_time_option_user_id', 'student_time_option');
        $this->dropForeignKey('student_time_option_time_option_id', 'student_time_option');

        $this->dropTable('{{%student_student_time_option}}');
    }
}
