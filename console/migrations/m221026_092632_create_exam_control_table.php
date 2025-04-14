<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_control}}`.
 */
class m221026_092632_create_exam_control_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_control';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_control');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%exam_control}}', [
            'id' => $this->primaryKey(),

            'time_table_id' => $this->integer()->notNull(),
            'start' => $this->integer()->null(),
            'start2' => $this->integer()->null(),
            'finish' => $this->integer()->null(),
            'finish2' => $this->integer()->null(),
            'max_ball' => $this->double()->null(),
            'max_ball2' => $this->double()->null(),
            'duration' => $this->integer()->null(),
            'duration2' => $this->integer()->null(),

            'question' => $this->text()->null(),
            'question2' => $this->text()->null(),
            'question_file' => $this->string(255)->null(),
            'question2_file' => $this->string(255)->null(),
            'course_id' => $this->integer()->null(),
            'semester_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->null(),
            'language_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'teacher_user_id' => $this->integer()->null(),
            'teacher_access_id' => $this->integer()->null(),
            'edu_semester_id' => $this->integer()->null(),
            'subject_category_id' => $this->integer()->null(),
            'archived' => $this->integer()->null(),
            'old_exam_control_id' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->null(),

            'faculty_id' => $this->integer()->null(),
            'direction_id' => $this->integer()->null(),
            'type' => $this->integer()->Null(),
            'category' => $this->tinyInteger(1)->defaultValue(1),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->null()->defaultValue(0),
            'updated_by' => $this->integer()->null()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->null()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('exam_control_time_table_id', 'exam_control', 'time_table_id', 'time_table', 'id');
        $this->addForeignKey('exam_control_course_id', 'exam_control', 'course_id', 'course', 'id');
        $this->addForeignKey('exam_control_semester_id', 'exam_control', 'semester_id', 'semestr', 'id');
        $this->addForeignKey('exam_control_edu_year_id', 'exam_control', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('exam_control_subject_id', 'exam_control', 'subject_id', 'subject', 'id');
        $this->addForeignKey('exam_control_language_id', 'exam_control', 'language_id', 'language', 'id');
        $this->addForeignKey('exam_control_edu_plan_id', 'exam_control', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('exam_control_teacher_user_id', 'exam_control', 'teacher_user_id', 'users', 'id');
        $this->addForeignKey('exam_control_teacher_access_id', 'exam_control', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('exam_control_edu_semester_id', 'exam_control', 'edu_semester_id', 'edu_semestr', 'id');
        $this->addForeignKey('exam_control_subject_category_id', 'exam_control', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('exam_control_faculty_id', 'exam_control', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('exam_control_direction_id', 'exam_control', 'direction_id', 'direction', 'id');
        $this->addForeignKey('exam_control_edu_semestr_subject_id', 'exam_control', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        //        $this->addForeignKey('exam_control_old_exam_control_id', 'exam_control', 'old_exam_control_id', 'old_exam_control', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('exam_control_time_table_id', 'exam_control');
        $this->dropForeignKey('exam_control_course_id', 'exam_control');
        $this->dropForeignKey('exam_control_semester_id', 'exam_control');
        $this->dropForeignKey('exam_control_edu_year_id', 'exam_control');
        $this->dropForeignKey('exam_control_subject_id', 'exam_control');
        $this->dropForeignKey('exam_control_language_id', 'exam_control');
        $this->dropForeignKey('exam_control_edu_plan_id', 'exam_control');
        $this->dropForeignKey('exam_control_teacher_user_id', 'exam_control');
        $this->dropForeignKey('exam_control_teacher_access_id', 'exam_control');
        $this->dropForeignKey('exam_control_edu_semester_id', 'exam_control');
        $this->dropForeignKey('exam_control_subject_category_id', 'exam_control');
        $this->dropForeignKey('exam_control_faculty_id', 'exam_control');
        $this->dropForeignKey('exam_control_direction_id', 'exam_control');
        $this->dropForeignKey('exam_control_edu_semestr_subject_id', 'exam_control');
        //        $this->dropForeignKey('exam_control_old_exam_control_id', 'exam_control');

        $this->dropTable('{{%exam_control}}');
    }
}
