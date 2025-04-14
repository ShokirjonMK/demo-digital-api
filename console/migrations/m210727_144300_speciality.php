<?php

use yii\db\Migration;

/**
 * Class m210727_144300_speciality
 */
class m210727_144300_speciality extends Migration
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

        $this->createTable('{{%speciality}}', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger()->null()->defaultValue(0),
            'code' => $this->string(50),
            'sort' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_on' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_on' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        
        $this->createTable('{{%speciality_info}}', [
            'info_id' => $this->primaryKey(),
            'speciality_id' => $this->integer(),
            'language' => $this->string(100)->notNull(),
            'name' => $this->string(),
            'description' => $this->text(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%speciality_info}}');
        $this->dropTable('{{%speciality}}');
    }
}
