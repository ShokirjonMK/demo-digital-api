<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sub_question}}`.
 */
class m220223_061354_create_sub_question_table extends Migration
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
        $this->createTable('{{%sub_question}}', [
            'id' => $this->primaryKey(),

            'question' => $this->text()->notNull()->comment('Tirkama savol matni yozilati'),
            'question_id' => $this->integer()->notNull()->comment('Tirkama savol ushbu salovga choshimcha  yozilati'),

            'percent' => $this->integer()->notNull(),
            'ball' => $this->double()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('sqq_sub_question_question_mk', 'sub_question', 'question_id', 'question', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sqq_sub_question_question_mk', 'sub_question');

        $this->dropTable('{{%sub_question}}');
    }
}
