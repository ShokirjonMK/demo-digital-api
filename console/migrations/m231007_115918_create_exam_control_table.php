<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_control}}`.
 */
class m231007_115918_create_exam_control_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'exam_control';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_control');
        }

        $this->createTable('{{%exam_control}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->null(),
            'edu_semestr_exam_type_id' => $this->integer()->notNull(),
            'exam_type_id' => $this->integer()->null(),

            'start_time' => $this->integer()->notNull(),
            'finish_time' => $this->integer()->notNull(),
            'duration' => $this->integer()->notNull(),
            'max_ball' => $this->double()->notNull(),

            'question' => $this->text()->null(),
            'file' => $this->string(255)->null(),
            'question_count' => $this->integer()->null() ,

            'user_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'edu_plan_id' => $this->integer()->notNull(),
            'edu_semestr_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),

            'appeal_time' => $this->integer()->null(),
            'appeal_status' => $this->tinyInteger(1)->defaultValue(0),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ] , $tableOptions);

        $this->addForeignKey('mk_exam_control_table_group_table', 'exam_control', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_exam_control_table_subject_table', 'exam_control', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_exam_control_table_subject_category_table', 'exam_control', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('mk_exam_control_table_language_table', 'exam_control', 'language_id', 'languages', 'id');
        $this->addForeignKey('mk_exam_control_table_user_table', 'exam_control', 'user_id', 'users', 'id');
//        $this->addForeignKey('mk_exam_control_table_teacher_access_table', 'exam_control', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('mk_exam_control_table_faculty_table', 'exam_control', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_exam_control_table_direction_table', 'exam_control', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_exam_control_table_edu_plan_table', 'exam_control', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_exam_control_table_edu_semestr_table', 'exam_control', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_exam_control_table_edu_semestr_subject_table', 'exam_control', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_exam_control_table_edu_semestr_exams_type_table', 'exam_control', 'edu_semestr_exam_type_id', 'edu_semestr_exams_type', 'id');
        $this->addForeignKey('mk_exam_control_table_exam_type_table', 'exam_control', 'exam_type_id', 'exams_type', 'id');
//        $this->addForeignKey('mk_exam_control_table_time_table_table', 'exam_control', 'time_table_id', 'time_table', 'id');
        $this->addForeignKey('mk_exam_control_table_edu_year_table', 'exam_control', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_exam_control_table_course_table', 'exam_control', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_exam_control_table_semestr_table', 'exam_control', 'semestr_id', 'semestr', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_control}}');
    }
}
