<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_topic_test_answer}}`.
 */
class m230808_052814_create_student_topic_test_answer_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'student_topic_test_answer';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_topic_test_answer');
        }

        $this->createTable('{{%student_topic_test_answer}}', [
            'id' => $this->primaryKey(),

            'student_topic_result_id' => $this->integer()->notNull(),
            'subject_topic_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'test_id' => $this->integer()->notNull(),
            'answer_option_id' => $this->integer()->notNull(),

            'is_correct' => $this->tinyInteger(1)->notNull(),
            'options' => $this->string(255)->notNull(),

            'status' => $this->integer()->defaultValue(0),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_student_topic_test_answer_table_st_result_table', 'student_topic_test_answer', 'student_topic_result_id', 'student_topic_result', 'id');
        $this->addForeignKey('mk_student_topic_test_answer_table_subject_topic_table', 'student_topic_test_answer', 'subject_topic_id', 'subject_topic', 'id');
        $this->addForeignKey('mk_student_topic_test_answer_table_student_table', 'student_topic_test_answer', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_student_topic_test_answer_table_users_table', 'student_topic_test_answer', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_student_topic_test_answer_table_test_table', 'student_topic_test_answer', 'test_id', 'test', 'id');
        $this->addForeignKey('mk_student_topic_test_answer_table_answer_option_table', 'student_topic_test_answer', 'answer_option_id', 'option', 'id');

    }

    /**
     * {@inheritdoc}
     */


    public function safeDown()
    {
        $this->dropForeignKey('mk_student_topic_test_answer_table_st_result_table', 'student_topic_test_answer');
        $this->dropForeignKey('mk_student_topic_test_answer_table_subject_topic_table', 'student_topic_test_answer');
        $this->dropForeignKey('mk_student_topic_test_answer_table_student_table', 'student_topic_test_answer');
        $this->dropForeignKey('mk_student_topic_test_answer_table_users_table', 'student_topic_test_answer');
        $this->dropForeignKey('mk_student_topic_test_answer_table_test_table', 'student_topic_test_answer');
        $this->dropForeignKey('mk_student_topic_test_answer_table_answer_option_table', 'student_topic_test_answer');
        $this->dropTable('{{%student_topic_test_answer}}');
    }
}
