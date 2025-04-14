<?php

use yii\db\Migration;

/**
 * Class m200911_041001_counting
 */
class m200911_041001_counting extends Migration
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

        // Contents
        $this->createTable('{{%counting}}', [
            'count_id' => $this->primaryKey(),
            'item_id' => $this->integer()->defaultValue(0),
            'item_type' => $this->string(150)->notNull(),
            'item_count' => $this->integer()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%counting}}');
    }
}
