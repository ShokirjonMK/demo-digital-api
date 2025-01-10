<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_topic_result}}`.
 */
class m230808_050255_create_student_topic_result_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'student_topic_result';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_topic_result');
        }

        $this->createTable('{{%student_topic_result}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),
            'subject_topic_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),

            'question_count' => $this->integer()->defaultValue(0),
            'ball' => $this->integer()->defaultValue(0),
            'percent' => $this->float()->defaultValue(0),

            'start_time' => $this->integer()->notNull(),
            'end_time' => $this->integer()->notNull(),

            'group_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'course_id' => $this->integer()->null(),

            'status' => $this->integer()->defaultValue(0),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('mk_student_topic_result_table_subject_table', 'student_topic_result', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_student_topic_result_table_subject_topic_table', 'student_topic_result', 'subject_topic_id', 'subject_topic', 'id');
        $this->addForeignKey('mk_student_topic_result_table_users_table', 'student_topic_result', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_student_topic_result_table_student_table', 'student_topic_result', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_student_topic_result_table_group_table', 'student_topic_result', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_student_topic_result_table_edu_semestr_table', 'student_topic_result', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_student_topic_result_table_edu_year_table', 'student_topic_result', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_student_topic_result_table_course_table', 'student_topic_result', 'course_id', 'course', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_student_topic_result_table_subject_table', 'student_topic_result');
        $this->dropForeignKey('mk_student_topic_result_table_subject_topic_table', 'student_topic_result');
        $this->dropForeignKey('mk_student_topic_result_table_users_table', 'student_topic_result');
        $this->dropForeignKey('mk_student_topic_result_table_student_table', 'student_topic_result');
        $this->dropForeignKey('mk_student_topic_result_table_group_table', 'student_topic_result');
        $this->dropForeignKey('mk_student_topic_result_table_edu_semestr_table', 'student_topic_result');
        $this->dropForeignKey('mk_student_topic_result_table_edu_year_table', 'student_topic_result');
        $this->dropForeignKey('mk_student_topic_result_table_course_table', 'student_topic_result');
        $this->dropTable('{{%student_topic_result}}');
    }
}
