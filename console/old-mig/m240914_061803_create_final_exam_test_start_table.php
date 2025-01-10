<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%final_exam_test_start}}`.
 */
class m240914_061803_create_final_exam_test_start_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'final_exam_test_start';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('final_exam_test_start');
        }

        $this->createTable('{{%final_exam_test_start}}', [
            'id' => $this->primaryKey(),
            'final_exam_test_id' => $this->integer()->notNull(),
            'student_mark_id' => $this->integer()->null(),
            'student_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),

            'password' => $this->string(255)->notNull(),

            'start_time' => $this->integer()->null(),
            'finish_time' => $this->integer()->null(),

            'exam_type' => $this->integer()->defaultValue(0),
            'exam_form_type' => $this->integer()->null(),

            'correct' => $this->integer()->defaultValue(0),
            'attends_count' => $this->integer()->defaultValue(1),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_final_exam_test_start_table_final_exam_test_table', 'final_exam_test_start', 'final_exam_test_id', 'final_exam_test', 'id');
        $this->addForeignKey('mk_final_exam_test_start_table_student_mark_table', 'final_exam_test_start', 'student_mark_id', 'student_mark', 'id');
        $this->addForeignKey('mk_final_exam_test_start_table_student_table', 'final_exam_test_start', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_final_exam_test_start_table_users_table', 'final_exam_test_start', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%final_exam_test_start}}');
    }
}
