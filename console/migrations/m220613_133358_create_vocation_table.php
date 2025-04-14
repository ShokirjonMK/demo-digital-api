<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vocation}}`.
 */
class m220613_133358_create_vocation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'vocation';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('vocation');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%vocation}}', [
            'id' => $this->primaryKey(),

            'start_date' => $this->date()->notNull(),
            'finish_date' => $this->date()->notNull(),
            'symbol' => $this->string(5)->null(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->tinyInteger(2)->defaultValue(1)->comment("1- tatil, 2-kasal, 3-......"),
            'year' => $this->integer()->Null(),
            'month' => $this->integer()->Null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);
        $this->addForeignKey('vu_vocation_user', 'vocation', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('vu_vocation_user', 'vocation');
        $this->dropTable('{{%vocation}}');
    }
}
