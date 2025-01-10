<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timetable}}`.
 */
class m240206_053655_create_timetable_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'timetable';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('timetable');
        }

        $this->createTable('{{%timetable}}', [
            'id' => $this->primaryKey(),
            'ids' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
//            'teacher_access_id' => $this->integer()->notNull(),
//            'user_id' => $this->integer()->notNull(),
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
            'type' => $this->integer()->defaultValue(0),
            'two_group' => $this->integer()->defaultValue(0),
            'group_type' => $this->integer()->defaultValue(1),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ],$tableOptions);

        $this->createIndex(
            'idx-timetable-ids',
            'timetable',
            'ids'
        );

        $this->addForeignKey('mk_timetable_table_group_table', 'timetable', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_timetable_table_edu_semestr_subject_table', 'timetable', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
//        $this->addForeignKey('mk_timetable_table_teacher_access_table', 'timetable', 'teacher_access_id', 'teacher_access', 'id');
//        $this->addForeignKey('mk_timetable_table_user_table', 'timetable', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_timetable_table_subject_table', 'timetable', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_timetable_table_subject_category_table', 'timetable', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('mk_timetable_table_edu_plan_table', 'timetable', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_timetable_table_edu_semestr_table', 'timetable', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_timetable_table_edu_form_table', 'timetable', 'edu_form_id', 'edu_form', 'id');
        $this->addForeignKey('mk_timetable_table_edu_year_table', 'timetable', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_timetable_table_edu_type_table', 'timetable', 'edu_type_id', 'edu_type', 'id');
        $this->addForeignKey('mk_timetable_table_faculty_table', 'timetable', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_timetable_table_direction_table', 'timetable', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_timetable_table_semestr_table', 'timetable', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_timetable_table_course_table', 'timetable', 'course_id', 'course', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timetable}}');
    }
}
