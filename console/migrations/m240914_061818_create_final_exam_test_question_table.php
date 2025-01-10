<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%final_exam_test_question}}`.
 */
class m240914_061818_create_final_exam_test_question_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'final_exam_test_question';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('final_exam_test_question');
        }

        $this->createTable('{{%final_exam_test_question}}', [
            'id' => $this->primaryKey(),
            'final_exam_test_start_id' => $this->integer()->notNull(),
            'student_mark_id' => $this->integer()->null(),
            'student_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),

            'test_id' => $this->integer()->notNull(),
            'option_id' => $this->integer()->null(),

            'is_correct' => $this->integer()->defaultValue(0),
            'option' => $this->string(255)->defaultValue(0),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_final_exam_test_question_table_final_exam_test_start_table', 'final_exam_test_question', 'final_exam_test_start_id', 'final_exam_test_start', 'id');
        $this->addForeignKey('mk_final_exam_test_question_table_student_mark_table', 'final_exam_test_question', 'student_mark_id', 'student_mark', 'id');
        $this->addForeignKey('mk_final_exam_test_question_table_student_table', 'final_exam_test_question', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_final_exam_test_question_table_users_table', 'final_exam_test_question', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%final_exam_test_question}}');
    }
}
