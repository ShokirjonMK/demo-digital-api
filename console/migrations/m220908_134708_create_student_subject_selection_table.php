<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_subject_selection}}`.
 */
class m220908_134708_create_student_subject_selection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_subject_selection';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_subject_selection');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%student_subject_selection}}', [
            'id' => $this->primaryKey(),

            'edu_semestr_subject_id' => $this->integer(11)->notNull(),
            'student_id' => $this->integer(11)->notNull(),
            'edu_semester_id' => $this->integer(11)->Null(),
            'subject_id' => $this->integer(11)->Null(),
            'faculty_id' => $this->integer(11)->Null(),
            'edu_plan_id' => $this->integer(11)->Null(),
            'type' => $this->integer(11)->Null(),
            'user_id' => $this->integer(11)->Null(),
            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('student_subject_selection_user_id', 'student_subject_selection', 'user_id', 'users', 'id');
        $this->addForeignKey('student_subject_selection_edu_semestr_subject_id', 'student_subject_selection', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('student_subject_selection_edu_semester_id', 'student_subject_selection', 'edu_semester_id', 'edu_semestr', 'id');
        $this->addForeignKey('student_subject_selection_subject_id', 'student_subject_selection', 'subject_id', 'subject', 'id');
        $this->addForeignKey('student_subject_selection_edu_plan_id', 'student_subject_selection', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('student_subject_selection_faculty_id', 'student_subject_selection', 'faculty_id', 'faculty', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('student_subject_selection_user_id', 'student_subject_selection');
        $this->dropForeignKey('student_subject_selection_edu_semestr_subject_id', 'student_subject_selection');
        $this->dropForeignKey('student_subject_selection_edu_semester_id', 'student_subject_selection');
        $this->dropForeignKey('student_subject_selection_subject_id', 'student_subject_selection');
        $this->dropForeignKey('student_subject_selection_edu_plan_id', 'student_subject_selection');
        $this->dropForeignKey('student_subject_selection_faculty_id', 'student_subject_selection');

        $this->dropTable('{{%student_subject_selection}}');
    }
}
