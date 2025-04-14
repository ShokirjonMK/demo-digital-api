<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%poll_question}}`.
 */
class m240110_115519_create_poll_question_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'poll_question';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('poll_question');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%poll_question}}', [
            'id' => $this->primaryKey(),
            'poll_id' => $this->integer()->notNull(),
            'type' => $this->tinyInteger()->defaultValue(1),

            //// on translate
            // 'question' => $this->text()->notNull(),

            'order' => $this->tinyInteger()->defaultValue(1),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer()->defaultValue(0),
            'updated_by' => $this->integer()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('fk_poll_question_poll_id', '{{%poll_question}}', 'poll_id', '{{%poll}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_poll_question_poll_id', '{{%poll_question}}');

        $this->dropTable('{{%poll_question}}');
    }
}
