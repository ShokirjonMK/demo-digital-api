<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%turniket}}`.
 */
class m241021_042641_create_turniket_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'turniket';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('turniket');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }


        $this->createTable(
            '{{%turniket}}',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->null()->comment('User ID'),
                'turniket_id' => $this->integer()->null()->comment('User ID'),
                'date' => $this->date()->null()->comment('Date'),
                'passport_pin' => $this->string(255)->null()->comment('key'),


                'go_in_time' => $this->integer()->null()->comment('Go in time'),
                'go_out_time' => $this->integer()->null()->comment('Go out time'),

                'order' => $this->tinyInteger(1)->defaultValue(1),
                'status' => $this->tinyInteger(1)->defaultValue(1),
                'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
                'created_at' => $this->integer()->notNull()->defaultValue(0),
                'updated_at' => $this->integer()->notNull()->defaultValue(0),

                'created_by' => $this->integer()->notNull()->defaultValue(0),
                'updated_by' => $this->integer()->notNull()->defaultValue(0),
                'archived' => $this->integer()->notNull()->defaultValue(0),
            ],
            $tableOptions
        );

        $this->addForeignKey('turniket_user_id_mk', '{{%turniket}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
        // $this->addForeignKey('turniket_turniket_id_profile_id_mk', '{{%turniket}}', 'turniket_id', '{{%profile}}', 'turniket_id', 'CASCADE');
        // $this->addForeignKey('turniket_passport_pin_profile_id_mk', '{{%turniket}}', 'passport_pin', '{{%profile}}', 'passport_pin', 'CASCADE');
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('turniket_user_id_mk', '{{%turniket}}');
        // $this->dropForeignKey('turniket_turniket_id_profile_id_mk', '{{%turniket}}');
        // $this->dropForeignKey('turniket_passport_pin_profile_id_mk', '{{%turniket}}');
        $this->dropTable('{{%turniket}}');
    }
}
