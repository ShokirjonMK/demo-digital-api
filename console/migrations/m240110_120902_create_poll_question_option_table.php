<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%poll_question_option}}`.
 */
class m240110_120902_create_poll_question_option_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'poll_question_option';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('poll_question_option');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%poll_question_option}}', [
            'id' => $this->primaryKey(),
            'poll_id' => $this->integer()->notNull(),
            'poll_question_id' => $this->integer()->notNull(),
            'type' => $this->tinyInteger()->defaultValue(1),


            // option on translate
            // 'option' => $this->text()->notNull(),

            'order' => $this->tinyInteger()->defaultValue(1),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer()->defaultValue(0),
            'updated_by' => $this->integer()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('fk_poll_question_option_poll_id', '{{%poll_question_option}}', 'poll_id', '{{%poll}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_poll_question_option_poll_question_id', '{{%poll_question_option}}', 'poll_question_id', '{{%poll_question}}', 'id', 'CASCADE');
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_poll_question_option_poll_id', '{{%poll_question_option}}');
        $this->dropForeignKey('fk_poll_question_option_poll_question_id', '{{%poll_question_option}}');
        $this->dropTable('{{%poll_question_option}}');
    }
}
