<?php

use yii\db\Migration;

/**
 * Class m210810_164600_employee_cert
 */
class m210810_164600_employee_cert extends Migration
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

        $this->createTable('{{%employee_cert}}', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'score' => $this->decimal(),
            'given_at' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_cert}}');
    }
}
