<?php

use yii\db\Migration;

/**
 * Class m211012_141629_time_table
 */
class m211012_141629_time_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'time_table';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('time_table');
        }

        $this->createTable('time_table', [
            'id' => $this->primaryKey(),
            'edu_semestr_subject_id' => $this->integer()->null(),
            // 'edu_semester_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->notNull(),
            'teacher_access_id' => $this->integer()->notNull(),
            'room_id' => $this->integer()->notNull(),
            'para_id' => $this->integer()->notNull(),
            // 'week_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->notNull(),
            'semestr_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->notNull(),
            'lecture_id' => $this->integer()->null(),
            // 'parent_id' => $this->integer()->null(),
            'time_option_id' => $this->integer()->null(),
            'subject_category_id' => $this->integer()->null(),
            'teacher_user_id' => $this->integer()->null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('tt_time_table_teacher_access_id', 'time_table', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('rt_time_table_room_id', 'time_table', 'room_id', 'room', 'id');
        // // $this->addForeignKey('rt_time_table_week_id', 'time_table', 'week_id', 'week', 'id');
        $this->addForeignKey('rt_time_table_faculty_id', 'time_table', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('pt_time_table_para_id', 'time_table', 'para_id', 'para', 'id');
        $this->addForeignKey('ct_time_table_course_id', 'time_table', 'course_id', 'course', 'id');
        $this->addForeignKey('st_time_table_semestr_id', 'time_table', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('et_time_table_edu_year_id', 'time_table', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('st_time_table_edu_subject_id', 'time_table', 'subject_id', 'subject', 'id');
        $this->addForeignKey('lt_time_table_edu_language_id', 'time_table', 'language_id', 'languages', 'id');
        $this->addForeignKey('lt_time_table_edu_semestr_subject_id', 'time_table', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        // $this->addForeignKey('ttto_time_table_time_option_id', 'time_table', 'time_option_id', 'time_option', 'id');
        // $this->addForeignKey('wk_time_table_edu_semester_id', 'time_table', 'edu_semester_id', 'edu_semestr', 'id');
        $this->addForeignKey('wk_time_table_subject_category_id', 'time_table', 'subject_category_id', 'subject_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('tt_time_table_teacher_access_id', 'time_table');
        $this->dropForeignKey('rt_time_table_room_id', 'time_table');
        // $this->dropForeignKey('rt_time_table_week_id', 'time_table');
        $this->dropForeignKey('rt_time_table_faculty_id', 'time_table');
        $this->dropForeignKey('pt_time_table_para_id', 'time_table');
        $this->dropForeignKey('ct_time_table_course_id', 'time_table');
        $this->dropForeignKey('st_time_table_semestr_id', 'time_table');
        $this->dropForeignKey('et_time_table_edu_year_id', 'time_table');
        $this->dropForeignKey('st_time_table_edu_subject_id', 'time_table');
        $this->dropForeignKey('lt_time_table_edu_language_id', 'time_table');
        $this->dropForeignKey('lt_time_table_edu_semestr_subject_id', 'time_table');
        // $this->dropForeignKey('ttto_time_table_time_option_id', 'time_table');
        // $this->dropForeignKey('wk_time_table_edu_semester_id', 'time_table');
        $this->dropForeignKey('wk_time_table_subject_category_id', 'time_table');

        $this->dropTable('time_table');
    }
}
