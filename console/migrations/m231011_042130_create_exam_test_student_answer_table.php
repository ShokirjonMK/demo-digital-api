<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_test_student_answer}}`.
 */
class m231011_042130_create_exam_test_student_answer_table extends Migration
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
        $tableName = Yii::$app->db->tablePrefix . 'exam_test_student_answer';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_test_student_answer');
        }

        $this->createTable('{{%exam_test_student_answer}}', [
            'id' => $this->primaryKey(),
            'exam_control_student_id' => $this->integer()->notNull(),
            'exam_control_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'exam_test_id' => $this->integer()->notNull(),
            'answer_option_id' => $this->integer()->notNull(),
            'is_correct' => $this->tinyInteger(1)->defaultValue(0),
            'options' => $this->string(255)->notNull(),
            'exam_test_option_id' => $this->integer()->defaultValue(0),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ] , $tableOptions);

        $this->addForeignKey('mk_exam_test_student_answer_table_exam_control_s_table', 'exam_test_student_answer', 'exam_control_student_id', 'exam_control_student', 'id');
        $this->addForeignKey('mk_exam_test_student_answer_table_exam_control_table', 'exam_test_student_answer', 'exam_control_id', 'exam_control', 'id');
        $this->addForeignKey('mk_exam_test_student_answer_table_subject_table', 'exam_test_student_answer', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_exam_test_student_answer_table_student_table', 'exam_test_student_answer', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_exam_test_student_answer_table_users_table', 'exam_test_student_answer', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_exam_test_student_answer_table_exam_test_table', 'exam_test_student_answer', 'exam_test_id', 'test', 'id');
        $this->addForeignKey('mk_exam_test_student_answer_table_answer_option_table', 'exam_test_student_answer', 'answer_option_id', 'option', 'id');
//        $this->addForeignKey('mk_exam_test_student_answer_table_exam_test_option_table', 'exam_test_student_answer', 'exam_test_option_id', 'exam_test_option', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_test_student_answer}}');
    }
}
