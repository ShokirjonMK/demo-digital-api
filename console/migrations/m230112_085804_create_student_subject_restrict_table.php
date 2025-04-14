<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_subject_restrict}}`.
 */
class m230112_085804_create_student_subject_restrict_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_subject_restrict';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_subject_restrict');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%student_subject_restrict}}', [
            'id' => $this->primaryKey(),

            'student_id' => $this->integer(11)->notNull(),
            'edu_semestr_subject_id' => $this->integer(11)->notNull(),
            'description' => $this->text()->null(),

            'subject_id' => $this->integer(11)->null(),

            'semestr_id' => $this->integer(11)->null(),
            'edu_semestr_id' => $this->integer(11)->null(),
            'edu_plan_id' => $this->integer(11)->null(),
            'faculty_id' => $this->integer(11)->null(),
            'edu_year_id' => $this->integer(11)->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mark_student_subject_restrict___student_id', 'student_subject_restrict', 'student_id', 'student', 'id');
        $this->addForeignKey('mark_student_subject_restrict___edu_semestr_subject_id', 'student_subject_restrict', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mark_student_subject_restrict___subject_id', 'student_subject_restrict', 'subject_id', 'subject', 'id');

        $this->addForeignKey('mark_student_subject_restrict___semestr_id', 'student_subject_restrict', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mark_student_subject_restrict___edu_semestr_id', 'student_subject_restrict', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mark_student_subject_restrict___edu_plan_id', 'student_subject_restrict', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mark_student_subject_restrict___faculty_id', 'student_subject_restrict', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mark_student_subject_restrict___edu_year_id', 'student_subject_restrict', 'edu_year_id', 'edu_year', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mark_student_subject_restrict___student_id', 'student_subject_restrict');
        $this->dropForeignKey('mark_student_subject_restrict___edu_semestr_subject_id', 'student_subject_restrict');
        $this->dropForeignKey('mark_student_subject_restrict___subject_id', 'student_subject_restrict');
        $this->dropForeignKey('mark_student_subject_restrict___semestr_id', 'student_subject_restrict');
        $this->dropForeignKey('mark_student_subject_restrict___edu_semestr_id', 'student_subject_restrict');
        $this->dropForeignKey('mark_student_subject_restrict___edu_plan_id', 'student_subject_restrict');
        $this->dropForeignKey('mark_student_subject_restrict___faculty_id', 'student_subject_restrict');
        $this->dropForeignKey('mark_student_subject_restrict___edu_year_id', 'student_subject_restrict');

        $this->dropTable('{{%student_subject_restrict}}');
    }
}
