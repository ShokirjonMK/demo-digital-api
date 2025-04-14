<?php

use yii\db\Migration;

/**
 * Class m200622_041308_table_trashbox
 */
class m200622_041308_table_trashbox extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%trashbox}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'res_id' => $this->integer()->notNull(),
            'type' => $this->string(255)->notNull(),
            'data' => 'LONGTEXT',
            'created_on' => $this->dateTime(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%trashbox}}');
    }
}
