<?php

use yii\db\Migration;

/**
 * Class m210709_120200_employee_job
 */
class m210709_120200_employee_job extends Migration
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

        $this->createTable('{{%employee_job}}', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer()->notNull(),
            'company' => $this->string()->notNull(),
            'begin_date' => $this->timestamp()->notNull(),
            'begin_doc_no' => $this->string()->null(),
            'begin_doc_date' => $this->timestamp()->null(),
            'end_date' => $this->timestamp()->notNull(),
            'end_doc_no' => $this->string()->null(),
            'end_doc_date' => $this->timestamp()->null(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_job}}');
    }
}
