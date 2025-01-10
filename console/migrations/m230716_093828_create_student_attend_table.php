<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_attend}}`.
 */
class m230716_093828_create_student_attend_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'student_attend';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_attend');
        }

        $this->createTable('{{%student_attend}}', [
            'id' => $this->primaryKey(),
            'check' => $this->integer()->null()->defaultValue(0),
            'time_table_id' => $this->integer()->notNull(),
            'date' => $this->dateTime()->notNull(),
            'attend_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'reason' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('0 sababsiz 1 sababli'),
            'attend_reason_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'edu_semestr_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1 kuz 2 bahor'),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_student_attend_table_time_table_table', 'student_attend', 'time_table_id', 'time_table', 'id');
        $this->addForeignKey('mk_student_attend_table_attend_table', 'student_attend', 'attend_id', 'attend', 'id');
        $this->addForeignKey('mk_student_attend_table_student_table', 'student_attend', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_student_attend_table_subject_table', 'student_attend', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_student_attend_table_attend_reason_table', 'student_attend', 'attend_reason_id', 'attend_reason', 'id');
        $this->addForeignKey('mk_student_attend_table_subject_category_table', 'student_attend', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('mk_student_attend_table_edu_year_table', 'student_attend', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_student_attend_table_edu_semestr_table', 'student_attend', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_student_attend_table_faculty_table', 'student_attend', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_student_attend_table_course_table', 'student_attend', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_student_attend_table_edu_plan_table', 'student_attend', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_student_attend_table_semestr_table', 'student_attend', 'semestr_id', 'semestr', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_student_attend_table_time_table_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_attend_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_student_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_subject_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_attend_reason_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_subject_category_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_edu_year_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_edu_semestr_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_faculty_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_course_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_edu_plan_table', 'student_attend');
        $this->dropForeignKey('mk_student_attend_table_semestr_table', 'student_attend');
        $this->dropTable('{{%student_attend}}');
    }
}
