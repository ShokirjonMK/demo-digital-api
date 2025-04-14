<?php

use yii\db\Migration;

/**
 * Class m210803_123300_subject_topic
 */
class m210803_123300_subject_topic extends Migration
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

        $this->createTable('{{%subject_topic}}', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'hours' => $this->integer()->notNull(),
            'sort' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_on' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_on' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        
        $this->createTable('{{%subject_topic_info}}', [
            'info_id' => $this->primaryKey(),
            'subject_topic_id' => $this->integer(),
            'language' => $this->string(100)->notNull(),
            'name' => $this->string(),
            'description' => $this->text(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subject_topic_info}}');
        $this->dropTable('{{%subject_topic}}');
    }
}
