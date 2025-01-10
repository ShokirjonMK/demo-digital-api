<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timetable_date}}`.
 */
class m240206_060608_create_timetable_date_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'timetable_date';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('timetable_date');
        }

        $this->createTable('{{%timetable_date}}', [
            'id' => $this->primaryKey(),

            'timetable_id' => $this->integer()->notNull(),
            'ids_id' => $this->integer()->notNull(),
            'date' => $this->date()->notNull(),
            'building_id' => $this->integer()->notNull(),
            'room_id' => $this->integer()->notNull(),
            'week_id' => $this->integer()->notNull(),
            'para_id' => $this->integer()->notNull(),

            'group_id' => $this->integer()->notNull(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'teacher_access_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->null(),
            'subject_category_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),
            'edu_form_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'edu_type_id' => $this->integer()->null(),
            'faculty_id' => $this->integer()->null(),
            'direction_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),
            'group_type' => $this->integer()->defaultValue(1),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_timetable_date_table_timetable_table', 'timetable_date', 'timetable_id', 'timetable', 'id');
        $this->addForeignKey('mk_timetable_date_table_timetable_ids_table', 'timetable_date', 'ids_id', 'timetable', 'ids');
        $this->addForeignKey('mk_timetable_date_table_building_table', 'timetable_date', 'building_id', 'building', 'id');
        $this->addForeignKey('mk_timetable_date_table_room_table', 'timetable_date', 'room_id', 'room', 'id');
        $this->addForeignKey('mk_timetable_date_table_week_table', 'timetable_date', 'week_id', 'week', 'id');
        $this->addForeignKey('mk_timetable_date_table_para_table', 'timetable_date', 'para_id', 'para', 'id');

        $this->addForeignKey('mk_timetable_date_table_group_table', 'timetable_date', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_timetable_date_table_edu_semestr_subject_table', 'timetable_date', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_timetable_date_table_teacher_access_table', 'timetable_date', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('mk_timetable_date_table_user_table', 'timetable_date', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_timetable_date_table_subject_table', 'timetable_date', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_timetable_date_table_subject_category_table', 'timetable_date', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('mk_timetable_date_table_edu_plan_table', 'timetable_date', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_timetable_date_table_edu_semestr_table', 'timetable_date', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_timetable_date_table_edu_form_table', 'timetable_date', 'edu_form_id', 'edu_form', 'id');
        $this->addForeignKey('mk_timetable_date_table_edu_year_table', 'timetable_date', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_timetable_date_table_edu_type_table', 'timetable_date', 'edu_type_id', 'edu_type', 'id');
        $this->addForeignKey('mk_timetable_date_table_faculty_table', 'timetable_date', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_timetable_date_table_direction_table', 'timetable_date', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_timetable_date_table_semestr_table', 'timetable_date', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_timetable_date_table_course_table', 'timetable_date', 'course_id', 'course', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timetable_date}}');
    }
}
