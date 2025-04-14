<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_content}}`.
 */
class m220127_130811_create_subject_content_table extends Migration
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
        $this->createTable('{{%subject_content}}', [
            'id' => $this->primaryKey(),
            'content' => $this->text()->Null(),
            'type' => $this->integer()->notNull(),
            'subject_topic_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->null(),
            'description' => $this->string()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('scst_subject_content_subject_topic_mk', 'subject_content', 'subject_topic_id', 'subject_topic', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('scst_subject_content_subject_topic_mk', 'subject_content');

        $this->dropTable('{{%subject_content}}');
    }
}
