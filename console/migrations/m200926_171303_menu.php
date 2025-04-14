<?php

use yii\db\Migration;

/**
 * Class m200926_171303_menu
 */
class m200926_171303_menu extends Migration
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

        // Menu group
        $this->createTable('{{%menu_group}}', [
            'id' => $this->primaryKey(),
            'name' => $this->text()->notNull(),
            'group_key' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'sort' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_on' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_on' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        // Menu items
        $this->createTable('{{%menu_items}}', [
            'id' => $this->primaryKey(),
            'language' => $this->string(100)->notNull(),
            'data' => $this->text()->notNull(),
            'item_id' => $this->integer()->notNull()->defaultValue(0),
            'type' => $this->string(100)->notNull(),
            'parent_id' => $this->integer()->notNull()->defaultValue(0),
            'group_key' => $this->string(255)->notNull(),
            'sort' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%menu_group}}');
        $this->dropTable('{{%menu_items}}');
    }
}
