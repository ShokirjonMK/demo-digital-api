<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student}}`.
 */
class m231025_044951_create_exam_student_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'exam_student';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student');
        }
        $this->createTable('{{%exam_student}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->null(),
            'type' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'student_user_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->null(),
            'language_id' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->null(),
            'edu_semestr_exams_type_id' => $this->integer()->null(),
            'exam_type_id' => $this->integer()->null(),
            'max_ball' => $this->double()->defaultValue(0),
            'student_ball' => $this->double()->defaultValue(0),
            'faculty_id' => $this->integer()->null(),
            'direction_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),
            'exam_teacher_user_id' => $this->integer()->null(),

            'start_time' => $this->integer()->notNull(),
            'finish_time' => $this->integer()->notNull(),

            'description' => $this->text()->null(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_exam_student_table_exam_table', 'exam_student', 'exam_id', 'exam', 'id');
        $this->addForeignKey('mk_exam_student_table_group_table', 'exam_student', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_exam_student_table_student_table', 'exam_student', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_exam_student_table_student_user_table', 'exam_student', 'student_user_id', 'users', 'id');
        $this->addForeignKey('mk_exam_student_table_edu_plan_table', 'exam_student', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_exam_student_table_subject_table', 'exam_student', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_exam_student_table_language_table', 'exam_student', 'language_id', 'languages', 'id');
        $this->addForeignKey('mk_exam_student_table_edu_semestr_subject_table', 'exam_student', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_exam_student_table_exam_type_table', 'exam_student', 'exam_type_id', 'exams_type', 'id');
        $this->addForeignKey('mk_exam_student_table_faculty_table', 'exam_student', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_exam_student_table_direction_table', 'exam_student', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_exam_student_table_edu_semestr_table', 'exam_student', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_exam_student_table_semestr_table', 'exam_student', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_exam_student_table_course_table', 'exam_student', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_exam_student_table_exam_teacher_user_table', 'exam_student', 'exam_teacher_user_id', 'users', 'id');
        $this->addForeignKey('mk_exam_student_table_edu_semestr_exams_type_table', 'exam_student', 'edu_semestr_exams_type_id', 'edu_semestr_exams_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_student}}');
    }
}
