<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%final_exam}}`.
 */
class m240112_093859_create_final_exam_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'final_exam';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('final_exam');
        }

        $this->createTable('{{%final_exam}}', [
            'id' => $this->primaryKey(),
            'vedomst' => $this->integer()->defaultValue(1),
//            'group_id' => $this->integer()->notNull(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->null(),

            'edu_semestr_exams_type_id' => $this->integer()->null(),
            'exams_type_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'edu_form_id' => $this->integer()->null(),
            'faculty_id' => $this->integer()->null(),
            'direction_id' => $this->integer()->null(),
            'language_id' => $this->integer()->null(),

            'exam_type' => $this->integer()->defaultValue(0),
            'exam_form_type' => $this->integer()->notNull(),

            'date' => $this->date()->null(),
            'building_id' => $this->integer()->null(),
            'room_id' => $this->integer()->null(),
            'para_id' => $this->integer()->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

//        $this->addForeignKey('mk_final_exam_table_group_table', 'final_exam', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_final_exam_table_edu_semestr_subject_table', 'final_exam', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_final_exam_table_user_table', 'final_exam', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_final_exam_table_edu_semestr_exams_type_table', 'final_exam', 'edu_semestr_exams_type_id', 'edu_semestr_exams_type', 'id');
        $this->addForeignKey('mk_final_exam_table_exams_type_table', 'final_exam', 'exams_type_id', 'exams_type', 'id');
        $this->addForeignKey('mk_final_exam_table_subject_table', 'final_exam', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_final_exam_table_edu_plan_table', 'final_exam', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_final_exam_table_edu_semestr_table', 'final_exam', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_final_exam_table_edu_year_table', 'final_exam', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_final_exam_table_course_table', 'final_exam', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_final_exam_table_semestr_table', 'final_exam', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_final_exam_table_faculty_table', 'final_exam', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_final_exam_table_direction_table', 'final_exam', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_final_exam_table_language_table', 'final_exam', 'language_id', 'languages', 'id');
        $this->addForeignKey('mk_final_exam_table_edu_form_table', 'final_exam', 'edu_form_id', 'edu_form', 'id');
        $this->addForeignKey('mk_final_exam_table_para_table', 'final_exam', 'para_id', 'para', 'id');
        $this->addForeignKey('mk_final_exam_table_building_table', 'final_exam', 'building_id', 'building', 'id');
        $this->addForeignKey('mk_final_exam_table_room_table', 'final_exam', 'room_id', 'room', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%final_exam}}');
    }
}
