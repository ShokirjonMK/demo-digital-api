<?php

use yii\db\Migration;

/**
 * Class m250109_073637_creata_oferta_file_table
 */
class m250109_073637_creata_oferta_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'oferta_file';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('oferta_file');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%oferta_file}}', [
            'id' => $this->primaryKey(),
            // 'name' => $this->string(255)->notNull()->comment('Name '),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Type of the file: 1- all, 2staff, 3 student, 4 - others'),
            'file' => $this->string(255)->null()->comment('File path'),

            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Status: 0-inactive, 1-active'),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),

            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%oferta_file}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250109_073637_creata_oferta_file_table cannot be reverted.\n";

        return false;
    }
    */
}
