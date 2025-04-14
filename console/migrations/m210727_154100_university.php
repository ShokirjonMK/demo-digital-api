<?php

use yii\db\Migration;

/**
 * Class m210727_154100_university
 */
class m210727_154100_university extends Migration
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

        $this->createTable('{{%university}}', [
            'id' => $this->primaryKey(),
            'country_id' => $this->integer()->notNull(),
            'region_id' => $this->integer(),
            'district_id' => $this->integer(),
            'code' => $this->string(50)->null()->defaultValue(null),
            'type' => $this->tinyInteger()->null()->defaultValue(0),
            'sort' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_on' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_on' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        
        $this->createTable('{{%university_info}}', [
            'info_id' => $this->primaryKey(),
            'university_id' => $this->integer(),
            'language' => $this->string(100)->notNull(),
            'name' => $this->string(),
            'description' => $this->text(),
            'address' => $this->text(),
            'parent' => $this->string(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%university_info}}');
        $this->dropTable('{{%university}}');
    }
}
