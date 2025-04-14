<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_mark}}`.
 */
class m231025_063749_create_student_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_mark';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_mark');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%student_mark}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer(11)->notNull(),
            'subject_id' => $this->integer(11)->notNull(),

            'edu_semestr_id' => $this->integer(11)->notNull(),
            'edu_semestr_subject_id' => $this->integer(11)->notNull(),
            'course_id' => $this->integer(11)->null(),
            'semestr_id' => $this->integer(11)->null(),

            'edu_year_id' => $this->integer(11)->null(),
            'faculty_id' => $this->integer(11)->null(),
            'edu_plan_id' => $this->integer(11)->null(),

            'exam_control_student_ball' => $this->double()->null(),
            'exam_control_student_ball2' => $this->double()->null(),
            'exam_student_ball' => $this->double()->null(),
            'ball' => $this->double()->null(),

            'description' => $this->text()->null(),
            'data' => $this->json()->null(),
            
            'attempt' => $this->tinyInteger(1)->defaultValue(1),
            // 'order' => $this->integer()->notNull(),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('ms_s_student_mark_student', 'student_mark', 'student_id', 'student', 'id');
        $this->addForeignKey('ms_s_student_mark_subject', 'student_mark', 'subject_id', 'subject', 'id');
        $this->addForeignKey('ms_e_student_mark_edu_semestr', 'student_mark', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('ms_e_student_mark_edu_semestr_subject', 'student_mark', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('ms_c_student_mark_course', 'student_mark', 'course_id', 'course', 'id');
        $this->addForeignKey('ms_s_student_mark_semestr', 'student_mark', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('ms_e_student_mark_edu_year', 'student_mark', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('ms_f_student_mark_faculty', 'student_mark', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('ms_e_student_mark_edu_plan', 'student_mark', 'edu_plan_id', 'edu_plan', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ms_s_student_mark_student', 'student_mark');
        $this->dropForeignKey('ms_s_student_mark_subject', 'student_mark');
        $this->dropForeignKey('ms_e_student_mark_edu_semestr', 'student_mark');
        $this->dropForeignKey('ms_e_student_mark_edu_semestr_subject', 'student_mark');
        $this->dropForeignKey('ms_c_student_mark_course', 'student_mark');
        $this->dropForeignKey('ms_s_student_mark_semestr', 'student_mark');
        $this->dropForeignKey('ms_e_student_mark_edu_year', 'student_mark');
        $this->dropForeignKey('ms_f_student_mark_faculty', 'student_mark');
        $this->dropForeignKey('ms_e_student_mark_edu_plan', 'student_mark');

        $this->dropTable('{{%student_mark}}');
    }
}
