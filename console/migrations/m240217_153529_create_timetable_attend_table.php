<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timetable_attend}}`.
 */
class m240217_153529_create_timetable_attend_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'timetable_attend';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('timetable_attend');
        }

        $this->createTable('{{%timetable_attend}}', [
            'id' => $this->primaryKey(),
            'timetable_id' => $this->integer()->notNull(),
            'ids_id' => $this->integer()->null(),
            'timetable_date_id' => $this->integer()->notNull(),
            'date' => $this->date()->null(),
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
            'group_type' => $this->integer()->notNull(),

            'timetable_reason_id' => $this->integer()->null(),
            'reason' => $this->integer()->defaultValue(0),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ],$tableOptions);

        $this->addForeignKey('mk_timetable_attend_table_timetable_table', 'timetable_attend', 'timetable_id', 'timetable', 'id');
        $this->addForeignKey('mk_timetable_attend_table_timetable_ids_table', 'timetable_attend', 'ids_id', 'timetable', 'ids');
        $this->addForeignKey('mk_timetable_attend_table_timetable_date_table', 'timetable_attend', 'timetable_date_id', 'timetable_date', 'id');
        $this->addForeignKey('mk_timetable_attend_table_group_table', 'timetable_attend', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_timetable_attend_table_student_table', 'timetable_attend', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_timetable_attend_table_student_user_table', 'timetable_attend', 'student_user_id', 'users', 'id');
        $this->addForeignKey('mk_timetable_attend_table_edu_plan_table', 'timetable_attend', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_timetable_attend_table_faculty_table', 'timetable_attend', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_timetable_attend_table_edu_semestr_table', 'timetable_attend', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_timetable_attend_table_semestr_table', 'timetable_attend', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_timetable_attend_table_edu_form_table', 'timetable_attend', 'edu_form_id', 'edu_form', 'id');
        $this->addForeignKey('mk_timetable_attend_table_edu_type_table', 'timetable_attend', 'edu_type_id', 'edu_type', 'id');
        $this->addForeignKey('mk_timetable_attend_table_edu_year_table', 'timetable_attend', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_timetable_attend_table_timetable_reason_table', 'timetable_attend', 'timetable_reason_id', 'timetable_reason', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timetable_attend}}');
    }
}
