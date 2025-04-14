<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%work_rate}}`.
 */
class m220608_121732_create_work_rate_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'work_rate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('work_rate');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%work_rate}}', [
            'id' => $this->primaryKey(),

            'rate' => $this->double()->defaultValue(0),
            'weekly_hours' => $this->double()->defaultValue(0),
            'hour_day' => $this->double()->defaultValue(0),
            'daily_hours' => $this->json()->Null()->comment('{"1":8, "2":7, "3":7, "4":7, "5":7}'),
            'type' => $this->tinyInteger(1)->defaultValue(0),

            // 'name' => $this->string(255)->null(),
            // 'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%work_rate}}');
    }
}
