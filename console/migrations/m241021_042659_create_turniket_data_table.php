<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%turniket_data}}`.
 */
class m241021_042659_create_turniket_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'turniket_data';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('turniket_data');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%turniket_data}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null()->comment('User ID'),
            'turniket_id' => $this->integer()->null()->comment('Turniket ID'),
            'date' => $this->date()->null()->comment('Date'),
            'time' => $this->integer()->null()->defaultValue(0),
            'reader' => $this->integer()->null()->defaultValue(0),
            'in_out' => $this->integer()->null()->defaultValue(1),

            'passport_pin' => $this->string(255)->null()->comment('key'),
            'data' => $this->json()->null()->comment('data'),
            'key' => $this->string(255)->null()->comment('key'),
            'type' => $this->tinyInteger(2)->defaultValue(1)->comment('type'),
            'created_at' => $this->integer()->null()->defaultValue(0),
            'updated_at' => $this->integer()->null()->defaultValue(0),

            'created_by' => $this->integer()->null()->defaultValue(0),
            'updated_by' => $this->integer()->null()->defaultValue(0),

        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%turniket_data}}');
    }
}
