<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%poll_user}}`.
 */
class m240110_122439_create_poll_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'poll_user';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('poll_user');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%poll_user}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),

            'poll_id' => $this->integer()->notNull(),
            'poll_question_id' => $this->integer()->notNull(),
            'poll_question_option_id' => $this->integer()->null(),
            'poll_question_option_answer' => $this->text()->null(),

            'answer' => $this->text()->null(),
            'student_id' => $this->integer(),
            'faculty_id' => $this->integer(),
            'edu_form_id' => $this->integer(),

            'order' => $this->tinyInteger()->defaultValue(1),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer()->defaultValue(0),
            'updated_by' => $this->integer()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('fk_poll_user_user', '{{%poll_user}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_poll_user_poll', '{{%poll_user}}', 'poll_id', '{{%poll}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_poll_user_poll_question', '{{%poll_user}}', 'poll_question_id', '{{%poll_question}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_poll_user_poll_question_option', '{{%poll_user}}', 'poll_question_option_id', '{{%poll_question_option}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_poll_user_student', '{{%poll_user}}', 'student_id', '{{%student}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_poll_user_faculty', '{{%poll_user}}', 'faculty_id', '{{%faculty}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_poll_user_edu_form', '{{%poll_user}}', 'edu_form_id', '{{%edu_form}}', 'id', 'CASCADE', 'CASCADE');
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign key constraints
        $this->dropForeignKey('fk_poll_user_user', '{{%poll_user}}');
        $this->dropForeignKey('fk_poll_user_poll', '{{%poll_user}}');
        $this->dropForeignKey('fk_poll_user_poll_question', '{{%poll_user}}');
        $this->dropForeignKey('fk_poll_user_poll_question_option', '{{%poll_user}}');
        $this->dropForeignKey('fk_poll_user_student', '{{%poll_user}}');
        $this->dropForeignKey('fk_poll_user_faculty', '{{%poll_user}}');
        $this->dropForeignKey('fk_poll_user_edu_form', '{{%poll_user}}');

        $this->dropTable('{{%poll_user}}');
    }
}
