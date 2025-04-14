<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%currency_list}}`.
 */
class m200720_120652_create_currency_list_table extends Migration
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

        $this->createTable('{{%currency_list}}', [
            'id' => $this->primaryKey(),
            'currency_name' => $this->string(150),
            'currency_code' => $this->string(50),
            'sort' => $this->integer(),
            'status' => $this->integer(),
        ], $tableOptions);

        $this->createTable('currency_rates', [
            'id' => $this->primaryKey(),
            'ckey' => $this->string(100),
            'cname' => $this->string(150),
            'cfrom' => $this->string(100),
            'cto' => $this->string(100),
            'cvalue' => $this->float(),
            'cvbefore' => $this->float(),
            'update_on' => $this->dateTime(),
        ], $tableOptions);

        $this->insert('currency_list', [
            'currency_name' => 'EURO',
            'currency_code' => 'EUR',
            'sort' => 1,
            'status' => 1,
        ]);

        $this->insert('currency_list', [
            'currency_name' => 'RUSSIAN RUBLE',
            'currency_code' => 'RUB',
            'sort' => 2,
            'status' => 1,
        ]);

        $this->insert('currency_list', [
            'currency_name' => 'U.S DOLLAR',
            'currency_code' => 'USD',
            'sort' => 3,
            'status' => 1,
        ]);

        $this->insert('currency_list', [
            'currency_name' => 'UZBEKISTANI SOM',
            'currency_code' => 'UZS',
            'sort' => 4,
            'status' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%currency_list}}');
    }
}
