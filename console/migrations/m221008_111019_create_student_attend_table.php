<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_attend}}`.
 */
class m221008_111019_create_student_attend_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        // $this->execute("SET FOREIGN_KEY_CHECKS = 0;");

        $tableName = Yii::$app->db->tablePrefix . 'attend_reason';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('attend_reason');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%attend_reason}}', [
            'id' => $this->primaryKey(),
            'is_confirmed' => $this->tinyInteger(1)->Null(),
            'start' => $this->dateTime()->notNull(),
            'end' => $this->dateTime()->notNull(),
            'student_id' => $this->integer()->notNull(),

            'subject_id' => $this->integer()->null(),

            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'file' => $this->string()->null(),
            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $tableName = Yii::$app->db->tablePrefix . 'student_attend';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_attend');
        }


        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%student_attend}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'reason' => $this->tinyInteger(1)->defaultValue(0)->comment('0 sababsiz 1 sababli'),
            'attend_id' => $this->integer()->notNull(),
            'attend_reason_id' => $this->integer()->null(),

            'date' => $this->date()->notNull(),
            'time_table_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->notNull(),
            'time_option_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'edu_semestr_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'type' => $this->tinyInteger(1)->defaultValue(1)->comment('1 kuz 2 bohor'),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('as_mk_student_attend_attend_reason_id', 'student_attend', 'attend_reason_id', 'attend_reason', 'id');
        $this->addForeignKey('as_mk_student_attend_attend_id', 'student_attend', 'attend_id', 'attend', 'id');
        $this->addForeignKey('as_mk_student_attend_student_id', 'student_attend', 'student_id', 'student', 'id');
        $this->addForeignKey('as_mk_student_attend_time_table', 'student_attend', 'time_table_id', 'time_table', 'id');
        $this->addForeignKey('as_mk_student_attend_subject', 'student_attend', 'subject_id', 'subject', 'id');
        $this->addForeignKey('as_mk_student_attend_subject_category', 'student_attend', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('as_mk_student_attend_time_option', 'student_attend', 'time_option_id', 'time_option', 'id');
        $this->addForeignKey('as_mk_student_attend_edu_year', 'student_attend', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('as_mk_student_attend_edu_semestr', 'student_attend', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('as_mk_student_attend_faculty_id', 'student_attend', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('as_mk_student_attend_edu_plan_id', 'student_attend', 'edu_plan_id', 'edu_plan', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('as_mk_student_attend_attend_reason_id', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_attend_id', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_student_id', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_time_table', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_subject', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_subject_category', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_time_option', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_edu_year', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_edu_semestr', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_faculty_id', 'student_attend');
        $this->dropForeignKey('as_mk_student_attend_edu_plan_id', 'student_attend');

        $this->dropTable('{{%student_attend}}');
    }
}
