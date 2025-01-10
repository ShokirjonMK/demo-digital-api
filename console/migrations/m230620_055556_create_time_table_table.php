<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%time_table}}`.
 */
class m230620_055556_create_time_table_table extends Migration
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

        $this->createTable('{{%time_table}}', [
            'id' => $this->primaryKey(),
            'ids' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'group_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->null(),
            'faculty_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'edu_plan_id' => $this->integer()->notNull(),
            'edu_semestr_id' => $this->integer()->notNull(),
//            'fall_spring' => $this->tinyInteger(1)->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'edu_form_id' => $this->integer()->notNull(),
            'edu_type_id' => $this->integer()->notNull(),
            'teacher_access_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->null(),
            'week_id' => $this->integer()->notNull(),
            'para_id' => $this->integer()->notNull(),
            'building_id' => $this->integer()->notNull(),
            'room_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'language_id' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),

            'two_groups' => $this->tinyInteger(1)->null(),
            'group_type' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'start_study' => $this->date()->null(),
            'end_study' => $this->date()->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_time_table_table_group_table', 'time_table', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_time_table_table_subject_table', 'time_table', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_time_table_table_subject_category_table', 'time_table', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('mk_time_table_table_subject_faculty_table', 'time_table', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_time_table_table_subject_direction_table', 'time_table', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_time_table_table_subject_edu_plan_table', 'time_table', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_time_table_table_subject_edu_semestr_table', 'time_table', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_time_table_table_subject_edu_year_table', 'time_table', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_time_table_table_subject_teacher_access_table', 'time_table', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('mk_time_table_table_subject_user_table', 'time_table', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_time_table_table_subject_week_table', 'time_table', 'week_id', 'week', 'id');
        $this->addForeignKey('mk_time_table_table_subject_para_table', 'time_table', 'para_id', 'para', 'id');
        $this->addForeignKey('mk_time_table_table_subject_building_table', 'time_table', 'building_id', 'building', 'id');
        $this->addForeignKey('mk_time_table_table_subject_room_table', 'time_table', 'room_id', 'room', 'id');
        $this->addForeignKey('mk_time_table_table_subject_course_table', 'time_table', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_time_table_table_subject_semestr_table', 'time_table', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_time_table_table_subject_languages_table', 'time_table', 'language_id', 'languages', 'id');
        $this->addForeignKey('mk_time_table_table_subject_edu_semestr_subject_table', 'time_table', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');

        $this->addForeignKey('mk_time_table_table_edu_form_table', 'time_table', 'edu_form_id', 'edu_form', 'id');
        $this->addForeignKey('mk_time_table_table_edu_type_table', 'time_table', 'edu_type_id', 'edu_type', 'id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_time_table_table_group_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_category_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_faculty_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_direction_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_edu_plan_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_edu_semestr_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_edu_year_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_teacher_access_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_user_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_week_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_para_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_building_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_room_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_course_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_semestr_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_languages_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_subject_edu_semestr_subject_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_edu_form_table', 'time_table');
        $this->dropForeignKey('mk_time_table_table_edu_type_table', 'time_table');
        $this->dropTable('{{%time_table}}');
    }
}
