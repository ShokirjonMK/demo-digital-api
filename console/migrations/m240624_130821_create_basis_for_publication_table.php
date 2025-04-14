<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%basis_for_publication}}`.
 */
class m240624_130821_create_basis_for_publication_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Define the table name with the correct table prefix
        $tableName = Yii::$app->db->tablePrefix . 'basis_for_publication';

        // Check if the table already exists, and drop it if it does
        if (Yii::$app->db->getTableSchema($tableName, true) !== null) {
            $this->dropTable($tableName);
        }


        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%basis_for_publication}}', [
            'id' => $this->primaryKey(), // Primary key identifier

            'name' => $this->text()->null()->comment('Name of the basis for publication'),
            'date' => $this->date()->null()->comment('Date of the basis for publication'),
            'number' => $this->string(255)->null()->comment('Number associated with the basis for publication'),
            'file' => $this->string(255)->null()->comment('File related to the basis for publication'),

            'order' => $this->tinyInteger(1)->defaultValue(1)->comment('Order of publication'),
            'status' => $this->tinyInteger(1)->defaultValue(1)->comment('Status of the publication'),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0)->comment('Is the publication deleted'),
            'created_at' => $this->integer()->notNull()->defaultValue(0)->comment('Timestamp when the record was created'),
            'updated_at' => $this->integer()->notNull()->defaultValue(0)->comment('Timestamp when the record was last updated'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who created the record'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who last updated the record'),
            'archived' => $this->integer()->notNull()->defaultValue(0)->comment('Is the publication archived')
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%basis_for_publication}}');
    }
}
