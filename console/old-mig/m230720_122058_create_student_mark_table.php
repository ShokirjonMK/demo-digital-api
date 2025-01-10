<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_mark}}`.
 */
class m230720_122058_create_student_mark_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'student_mark';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_mark');
        }

        $this->createTable('{{%student_mark}}', [
            'id' => $this->primaryKey(),
            'edu_semestr_exams_type_id' => $this->integer()->notNull(),
            'exam_type_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'student_user_id' => $this->integer()->notNull(),
            'ball' => $this->integer()->defaultValue(0),
            'max_ball' => $this->integer()->notNull(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),

            'subject_id' => $this->integer()->notNull(),
            'edu_plan_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),
            'faculty_id' => $this->integer()->null(),
            'direction_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),

            'type' => $this->integer()->defaultValue(0),

            'passed' => $this->integer()->null(),
            'attend' => $this->integer()->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_student_mark_table_edu_semestr_exams_type_table', 'student_mark', 'edu_semestr_exams_type_id', 'edu_semestr_exams_type', 'id');
        $this->addForeignKey('mk_student_mark_table_exam_type_table', 'student_mark', 'exam_type_id', 'exams_type', 'id');
        $this->addForeignKey('mk_student_mark_table_student_table', 'student_mark', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_student_mark_table_student_user_table', 'student_mark', 'student_user_id', 'users', 'id');
        $this->addForeignKey('mk_student_mark_table_group_table', 'student_mark', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_student_mark_table_edu_semestr_subject_table', 'student_mark', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_student_mark_table_subject_table', 'student_mark', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_student_mark_table_edu_plan_table', 'student_mark', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_student_mark_table_edu_semestr_table', 'student_mark', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_student_mark_table_faculty_table', 'student_mark', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_student_mark_table_direction_table', 'student_mark', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_student_mark_table_semestr_table', 'student_mark', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_student_mark_table_course_table', 'student_mark', 'course_id', 'course', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_student_mark_table_exam_type_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_student_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_group_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_edu_semestr_subject_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_subject_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_edu_plan_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_edu_semestr_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_faculty_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_direction_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_semestr_table', 'student_mark');
        $this->dropForeignKey('mk_student_mark_table_course_table', 'student_mark');
        $this->dropTable('{{%student_mark}}');
    }
}
