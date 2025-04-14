<?php

use yii\db\Migration;

/**
 * Class m200817_131435_content
 */
class m200817_131435_content extends Migration
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

        // Content
        $this->createTable('{{%site_content}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(100)->notNull(),
            'parent_id' => $this->integer()->defaultValue(0),
            'settings' => $this->json(),
            'template' => $this->string(255)->notNull(),
            'layout' => $this->string(255)->notNull(),
            'view' => $this->string(255)->notNull(),
            'resource_type' => $this->string(255)->notNull(),
            'sort' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'cacheable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'searchable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_on' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_on' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        // Content infos
        $this->createTable('{{%site_content_info}}', [
            'info_id' => $this->primaryKey(),
            'content_id' => $this->integer()->defaultValue(0),
            'language' => $this->string(100)->notNull(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'content' => 'LONGTEXT',
            'content_blocks' => $this->json(),
            'icon' => $this->string(255)->notNull(),
            'image' => $this->string(255)->notNull(),
            'cover_image' => $this->string(255)->notNull(),
            'meta' => $this->json(),
        ], $tableOptions);

        // Content fields
        $this->createTable('{{%site_content_field}}', [
            'field_id' => $this->primaryKey(),
            'content_id' => $this->integer()->defaultValue(0),
            'field_key' => $this->string(255)->notNull(),
            'field_value' => $this->text(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%site_content}}');
        $this->dropTable('{{%site_content_info}}');
        $this->dropTable('{{%site_content_field}}');
    }
}
