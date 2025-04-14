<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_question_option}}`.
 */
class m211120_103709_create_exam_question_option_table extends Migration
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
        $this->createTable('{{%exam_question_option}}', [
            'id' => $this->primaryKey(),
            'exam_question_id' => $this->integer()->notNull(),
            
            // name in translate
            'file' => $this->string(255)->Null(),
            'is_correct' => $this->tinyInteger(1)->defaultValue(0),
            'option' => $this->text()->notNull(),

            'order' => $this->tinyInteger(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('ses_exam_question_option_exam_question', 'exam_question_option', 'exam_question_id', 'exam_question', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('ses_exam_question_option_exam_question', 'exam_question_option');
      
        $this->dropTable('{{%exam_question_option}}');
    }
}
