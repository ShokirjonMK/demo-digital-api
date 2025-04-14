<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%area}}`.
 */
class m211018_162656_create_area_table extends Migration
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
        $this->createTable('{{%area}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string('150'),
            'region_id' => $this->integer(),
            'type' => $this->tinyInteger(1)->defaultValue(0),
            'postcode' => $this->string('150'),
            'lat' => $this->string('100'),
            'long' => $this->string('100'),
            'sort' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'created_on' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_on' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex(
            'idx-area-region_id',
            'area',
            'region_id'
        );

        $this->addForeignKey(
            'fk-area-region_id',
            'area',
            'region_id',
            'region',
            'id',
            'CASCADE'
        );

        $sql = file_get_contents(__DIR__ . '/../sql/area_insert.sql');
        \Yii::$app->db->pdo->exec($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-area-region_id',
            'area'
        );

        $this->dropIndex(
            'idx-area-region_id',
            'area'
        );


        $this->dropTable('{{%area}}');
    }
}
