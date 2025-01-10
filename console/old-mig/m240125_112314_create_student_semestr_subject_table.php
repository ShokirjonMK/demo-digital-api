<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_semestr_subject}}`.
 */
class m240125_112314_create_student_semestr_subject_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'student_semestr_subject';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_semestr_subject');
        }

        $this->createTable('{{%student_semestr_subject}}', [
            'id' => $this->primaryKey(),
            'edu_plan_id' => $this->integer()->notNull(),
            'edu_semestr_id' => $this->integer()->notNull(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'student_user_id' => $this->integer()->null(),

            'faculty_id' => $this->integer()->null(),
            'direction_id' => $this->integer()->null(),
            'edu_form_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'all_ball' => $this->float()->defaultValue(0),
            'closed' => $this->integer()->defaultValue(0),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_student_semestr_subject_table_edu_semestr_subject_table', 'student_semestr_subject', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_edu_plan_table', 'student_semestr_subject', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_edu_semestr_table', 'student_semestr_subject', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_student_table', 'student_semestr_subject', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_student_user_table', 'student_semestr_subject', 'student_user_id', 'users', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_faculty_table', 'student_semestr_subject', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_direction_table', 'student_semestr_subject', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_edu_form_table', 'student_semestr_subject', 'edu_form_id', 'edu_form', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_edu_year_table', 'student_semestr_subject', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_course_table', 'student_semestr_subject', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_student_semestr_subject_table_semestr_table', 'student_semestr_subject', 'semestr_id', 'semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%student_semestr_subject}}');
    }
}
