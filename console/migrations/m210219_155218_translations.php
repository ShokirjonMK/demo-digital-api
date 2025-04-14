<?php

use yii\db\Migration;

/**
 * Class m210219_155218_translations
 */
class m210219_155218_translations extends Migration
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
        $this->createTable('{{%translations}}', [
            'id' => $this->primaryKey(),
            'path_key' => $this->string(255),
            'lang_key' => $this->string(255),
            'translations' => $this->json()->defaultValue(null),
            'logs' => $this->json()->defaultValue(null),
            'updated_on' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%translations}}');
    }
}
