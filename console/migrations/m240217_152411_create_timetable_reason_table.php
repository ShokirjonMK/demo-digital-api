<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timetable_reason}}`.
 */
class m240217_152411_create_timetable_reason_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'timetable_reason';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('timetable_reason');
        }

        $this->createTable('{{%timetable_reason}}', [
            'id' => $this->primaryKey(),
            'is_confirmed' => $this->integer()->defaultValue(0),
            'start' => $this->dateTime()->notNull(),
            'end' => $this->dateTime()->notNull(),

            'group_id' => $this->integer()->null(),
            'student_id' => $this->integer()->null(),
            'student_user_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'faculty_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'edu_form_id' => $this->integer()->null(),
            'edu_type_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),

            'file' => $this->string(255)->null(),
            'description' => $this->text()->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ],$tableOptions);

        $this->addForeignKey('mk_timetable_reason_table_group_table', 'timetable_reason', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_timetable_reason_table_student_table', 'timetable_reason', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_timetable_reason_table_student_user_table', 'timetable_reason', 'student_user_id', 'users', 'id');
        $this->addForeignKey('mk_timetable_reason_table_edu_plan_table', 'timetable_reason', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_timetable_reason_table_faculty_table', 'timetable_reason', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_timetable_reason_table_edu_semestr_table', 'timetable_reason', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_timetable_reason_table_semestr_table', 'timetable_reason', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_timetable_reason_table_edu_form_table', 'timetable_reason', 'edu_form_id', 'edu_form', 'id');
        $this->addForeignKey('mk_timetable_reason_table_edu_type_table', 'timetable_reason', 'edu_type_id', 'edu_type', 'id');
        $this->addForeignKey('mk_timetable_reason_table_edu_year_table', 'timetable_reason', 'edu_year_id', 'edu_year', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timetable_reason}}');
    }
}
