<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_control_student}}`.
 */
class m221026_092659_create_exam_control_student_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_control_student';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_control_student');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%exam_control_student}}', [
            'id' => $this->primaryKey(),

            'exam_control_id' => $this->integer()->null(),
            'student_id' => $this->integer()->null(),
            'answer' => $this->text()->null(),
            'answer_file' => $this->string(255)->null(),
            'conclution' => $this->text()->null(),
            'answer2' => $this->text()->null(),
            'answer2_file' => $this->string(255)->null(),
            'conclution2' => $this->text()->null(),

            'course_id' => $this->integer()->null(),
            'semester_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->null(),
            'language_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'teacher_user_id' => $this->integer()->null(),
            'edu_semester_id' => $this->integer()->null(),
            'subject_category_id' => $this->integer()->null(),
            'archived' => $this->integer()->null(),
            'old_exam_control_id' => $this->integer()->null(),

            'ball' => $this->double()->null(),
            'ball2' => $this->double()->null(),
            'main_ball' => $this->double()->null(),
            'plagiat_percent' => $this->double()->null(),
            'plagiat2_percent' => $this->double()->null(),
            'plagiat_file' => $this->string(255)->null(),
            'plagiat2_file' => $this->string(255)->null(),

            'duration' => $this->integer()->null(),
            'start' => $this->integer()->null(),
            'faculty_id' => $this->integer()->null(),
            'direction_id' => $this->integer()->null(),
            'type' => $this->integer()->Null(),
            'category' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->null(),
            'student_mark_id' => $this->integer()->null(),

            'is_checked' => $this->tinyInteger(1)->defaultValue(0),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('exam_control_student_exam_control_id', 'exam_control_student', 'exam_control_id', 'exam_control', 'id');
        $this->addForeignKey('exam_control_student_course_id', 'exam_control_student', 'course_id', 'course', 'id');
        $this->addForeignKey('exam_control_student_semester_id', 'exam_control_student', 'semester_id', 'semestr', 'id');
        $this->addForeignKey('exam_control_student_edu_year_id', 'exam_control_student', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('exam_control_student_subject_id', 'exam_control_student', 'subject_id', 'subject', 'id');
        $this->addForeignKey('exam_control_student_language_id', 'exam_control_student', 'language_id', 'language', 'id');
        $this->addForeignKey('exam_control_student_edu_plan_id', 'exam_control_student', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('exam_control_student_teacher_user_id', 'exam_control_student', 'teacher_user_id', 'users', 'id');
        $this->addForeignKey('exam_control_student_edu_semester_id', 'exam_control_student', 'edu_semester_id', 'edu_semestr', 'id');
        $this->addForeignKey('exam_control_student_subject_category_id', 'exam_control_student', 'subject_category_id', 'subject_category', 'id');
        //        $this->addForeignKey('exam_control_student_old_exam_control_id', 'exam_control_student', 'old_exam_control_id', 'old_exam_control', 'id');
        $this->addForeignKey('exam_control_student_faculty_id', 'exam_control_student', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('exam_control_student_direction_id', 'exam_control_student', 'direction_id', 'direction', 'id');
        $this->addForeignKey('exam_control_student_student_id', 'exam_control_student', 'student_id', 'student', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('exam_control_student_exam_control_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_course_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_semester_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_edu_year_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_subject_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_language_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_edu_plan_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_teacher_user_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_edu_semester_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_subject_category_id', 'exam_control_student');
        //        $this->dropForeignKey('exam_control_student_old_exam_control_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_faculty_id', 'exam_control_student');
        $this->dropForeignKey('exam_control_student_direction_id', 'exam_control_student');
        $this->dropTable('{{%exam_control_student}}');
    }
}
