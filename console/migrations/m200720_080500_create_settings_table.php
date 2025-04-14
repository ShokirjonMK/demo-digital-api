<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%settings}}`.
 */
class m200720_080500_create_settings_table extends Migration
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

        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string('255'),
            'settings_key' => $this->string('255'),
            'settings_value' => 'mediumtext',
            'settings_group' => $this->string('255'),
            'settings_type' => $this->string('255'),
            'description' => $this->text(),
            'status' => $this->integer(),
            'sort' => $this->integer(),
            'required' => 'tinyint not null',
            'updated_on' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('{{%settings_translation}}', [
            'id' => $this->primaryKey(),
            'language' => $this->string('100'),
            'settings_key' => $this->string('255'),
            'settings_value' => 'mediumtext',
            'updated_on' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);

        $sql = file_get_contents(__DIR__ . '/../sql/settings.sql');
        \Yii::$app->db->pdo->exec($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
        $this->dropTable('{{%settings_translation}}');
    }
}
