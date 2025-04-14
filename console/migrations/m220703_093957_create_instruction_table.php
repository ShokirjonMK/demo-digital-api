<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%instruction}}`.
 */
class m220703_093957_create_instruction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'instruction';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('instruction');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%instruction}}', [
            'id' => $this->primaryKey(),
            'file_url' => $this->string(255),
            'key' => $this->string(255)->null()->unique(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%instruction}}');
    }
}
