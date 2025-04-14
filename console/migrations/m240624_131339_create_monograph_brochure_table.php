<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%monograph_brochure}}`.
 */
class m240624_131339_create_monograph_brochure_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'monograph_brochure';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('monograph_brochure');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%monograph_brochure}}', [
            'id' => $this->primaryKey(), //Primary key
            'name' => $this->text()->null()->comment('The name or title of the monograph or brochure'),
            'keys' => $this->string(255)->null()->comment('Keywords associated with the monograph or brochure'),
            'co_author_user_ids' => $this->json()->null()->comment('User IDs of co-authors'),
            'co_authors' => $this->text()->null()->comment('Names of co-authors'),
            'pages' => $this->integer()->null()->comment('Number of pages'),
            'basis_for_publication_id' => $this->integer()->null()->comment('ID of the basis for publication'),
            'dio' => $this->string(255)->null()->comment('DOI number'),
            'udk' => $this->string(255)->null()->comment('UDK number'),
            'bbk' => $this->string(255)->null()->comment('BBK number'),
            'isbn' => $this->string(255)->null()->comment('ISBN number'),
            'publisher_name' => $this->text()->null()->comment('Name of the publisher'),
            'file' => $this->string(255)->null()->comment('File associated with the monograph or brochure'),
            'translator' => $this->text()->null()->comment('Name of the translator'),
            'user_id' => $this->integer()->notNull()->comment('ID of the user who created the record'),

            'in_library' => $this->tinyInteger(1)->defaultValue(0)->comment('Is the item in the library (0 = no, 1 = yes)'),
            'order' => $this->tinyInteger(1)->defaultValue(1)->comment('Order of the item'),
            'status' => $this->tinyInteger(1)->defaultValue(1)->comment('Status of the item (1 = active, 0 = inactive)'),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0)->comment('Is the item deleted (0 = no, 1 = yes)'),
            'created_at' => $this->integer()->notNull()->defaultValue(0)->comment('Creation timestamp'),
            'updated_at' => $this->integer()->notNull()->defaultValue(0)->comment('Update timestamp'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who created the record'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who last updated the record'),
            'archived' => $this->integer()->notNull()->defaultValue(0)->comment('Is the item archived (0 = no, 1 = yes)'),
        ], $tableOptions);

        $this->addForeignKey('fk-monograph_brochure-user_id', '{{%monograph_brochure}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-monograph_brochure-basis_for_publication_id', '{{%monograph_brochure}}', 'basis_for_publication_id', '{{%basis_for_publication}}', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-monograph_brochure-user_id', '{{%monograph_brochure}}');
        $this->dropForeignKey('fk-monograph_brochure-basis_for_publication_id', '{{%monograph_brochure}}');

        $this->dropTable('{{%monograph_brochure}}');
    }
}
