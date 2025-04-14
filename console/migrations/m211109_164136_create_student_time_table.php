<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_time}}`.
 */
class m211109_164136_create_student_time_table extends Migration
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

        
        $tableName = Yii::$app->db->tablePrefix . 'student_time_table';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_time_table');
        }
      
        $this->createTable('{{%student_time_table}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'time_table_id' => $this->integer()->notNull(),
            'edu_plan_id' => $this->integer()->null(),
            'building_id' => $this->integer()->null(),
            'time_table_parent_id' => $this->integer()->null(),
            'time_table_lecture_id' => $this->integer()->null(),
            'teacher_user_id' => $this->integer()->null(),
            'time_option_id' => $this->integer()->null(),
            'student_time_option_id' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->null(),

            'teacher_access_id' => $this->integer()->null(),
            'language_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),
            'semester_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->null(),
            'room_id' => $this->integer()->null(),
            'para_id' => $this->integer()->null(),
            'week_id' => $this->integer()->null(),
            'edu_semester_id' => $this->integer()->null(),
            'subject_category_id' => $this->integer()->null(),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        // $this->addForeignKey('std_time_table_student', 'student_time_table', 'student_id', 'student', 'id');
        // $this->addForeignKey('std_time_table_time_table', 'student_time_table', 'time_table_id', 'time_table', 'id');
        // $this->addForeignKey('std_time_table_edu_plan', 'student_time_table', 'edu_plan_id', 'edu_plan', 'id');
        // $this->addForeignKey('std_time_table_building', 'student_time_table', 'building_id', 'building', 'id');
        // // $this->addForeignKey('std_time_table_time_table_parent', 'student_time_table', 'time_table_parent_id', 'time_table', 'parent_id');
        // // $this->addForeignKey('std_time_table_time_table_lecture', 'student_time_table', 'time_table_lecture_id', 'time_table', 'lecture_id');
        // $this->addForeignKey('std_time_table_teacher_user', 'student_time_table', 'teacher_user_id', 'users', 'id');
        // $this->addForeignKey('std_time_table_time_option', 'student_time_table', 'time_option_id', 'time_option', 'id');
        // $this->addForeignKey('std_time_table_student_time_option', 'student_time_table', 'student_time_option_id', 'student_time_option', 'id');
        // $this->addForeignKey('std_time_table_teacher_access', 'student_time_table', 'teacher_access_id', 'teacher_access', 'id');
        // $this->addForeignKey('std_time_table_language', 'student_time_table', 'language_id', 'language', 'id');
        // $this->addForeignKey('std_time_table_course', 'student_time_table', 'course_id', 'course', 'id');
        // $this->addForeignKey('std_time_table_semester', 'student_time_table', 'semester_id', 'semester', 'id');
        // $this->addForeignKey('std_time_table_edu_year', 'student_time_table', 'edu_year_id', 'edu_year', 'id');
        // $this->addForeignKey('std_time_table_subject', 'student_time_table', 'subject_id', 'subject', 'id');
        // $this->addForeignKey('std_time_table_room', 'student_time_table', 'room_id', 'room', 'id');
        // $this->addForeignKey('std_time_table_para', 'student_time_table', 'para_id', 'para', 'id');
        // $this->addForeignKey('std_time_table_week', 'student_time_table', 'week_id', 'week', 'id');
        // $this->addForeignKey('std_time_table_edu_semester', 'student_time_table', 'edu_semester_id', 'edu_semester', 'id');
        // $this->addForeignKey('std_time_table_subject_category', 'student_time_table', 'subject_category_id', 'subject_category', 'id');
        // $this->addForeignKey('std_time_table_edu_semestr_subject_id', 'student_time_table', 'table_edu_semestr_subject_id', 'edu_semestr_subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropForeignKey('std_time_table_student', 'student_time_table');
        // $this->dropForeignKey('std_time_table_time_table', 'student_time_table');
        // $this->dropForeignKey('std_time_table_edu_plan', 'student_time_table');
        // $this->dropForeignKey('std_time_table_building', 'student_time_table');
        // // $this->dropForeignKey('std_time_table_time_table_parent', 'student_time_table');
        // // $this->dropForeignKey('std_time_table_time_table_lecture', 'student_time_table');
        // $this->dropForeignKey('std_time_table_teacher_user', 'student_time_table');
        // $this->dropForeignKey('std_time_table_time_option', 'student_time_table');
        // $this->dropForeignKey('std_time_table_student_time_option', 'student_time_table');
        // $this->dropForeignKey('std_time_table_teacher_access', 'student_time_table');
        // $this->dropForeignKey('std_time_table_language', 'student_time_table');
        // $this->dropForeignKey('std_time_table_course', 'student_time_table');
        // $this->dropForeignKey('std_time_table_semester', 'student_time_table');
        // $this->dropForeignKey('std_time_table_edu_year', 'student_time_table');
        // $this->dropForeignKey('std_time_table_subject', 'student_time_table');
        // $this->dropForeignKey('std_time_table_room', 'student_time_table');
        // $this->dropForeignKey('std_time_table_para', 'student_time_table');
        // $this->dropForeignKey('std_time_table_week', 'student_time_table');
        // $this->dropForeignKey('std_time_table_edu_semester', 'student_time_table');
        // $this->dropForeignKey('std_time_table_subject_category', 'student_time_table');
        // $this->dropForeignKey('std_time_table_edu_semestr_subject_id', 'student_time_table');

        $this->dropTable('{{%student_time_table}}');
    }
}
