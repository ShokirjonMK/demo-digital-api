<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam}}`.
 */
class m231021_062634_create_exam_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'exam';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam');
        }

        $this->createTable('{{%exam}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->null(),
            'type' => $this->integer()->notNull(),
            'edu_plan_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'exam_type_id' => $this->integer()->notNull(),
            'start_time' => $this->integer()->notNull(),
            'finish_time' => $this->integer()->notNull(),
            'duration' => $this->integer()->null(),
            'max_ball' => $this->double()->defaultValue(0),
            'is_confirm' => $this->integer()->defaultValue(0),

            'question' => $this->text()->null(),
            'file' => $this->string(255)->null(),
            'question_count' => $this->integer()->null(),

            'faculty_id' => $this->integer()->null(),
            'direction_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),

            'description' => $this->text()->null(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ] , $tableOptions);
        $this->addForeignKey('mk_exam_table_parent_table', 'exam', 'parent_id', 'exam', 'id');
        $this->addForeignKey('mk_exam_table_subject_table', 'exam', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_exam_table_edu_semestr_subject_table', 'exam', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_exam_table_exam_type_table', 'exam', 'exam_type_id', 'exams_type', 'id');
        $this->addForeignKey('mk_exam_table_faculty_table', 'exam', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_exam_table_direction_table', 'exam', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_exam_table_edu_plan_table', 'exam', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_exam_table_edu_year_table', 'exam', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_exam_table_edu_semestr_table', 'exam', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_exam_table_course_table', 'exam', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_exam_table_semestr_table', 'exam', 'semestr_id', 'semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam}}');
    }
}
