<?php

use yii\db\Migration;

/**
 * Class m220610_061250_partiya
 */
class m220610_061250_partiya extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'partiya';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('partiya');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%partiya}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->null(),
            'name_ru' => $this->string(255)->null(),
            'name_en' => $this->string(255)->null(),
            'description' => $this->text()->null(),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->insert('partiya', array(
            'id' => '1',
            'name' => "Yo'q",
        ));

        $this->insert('partiya', array(
            'id' => '2',
            'name' => "XDP",
        ));

        $this->insert('partiya', array(
            'id' => '3',
            'name' => "Adolat SDP",
        ));

        $this->insert('partiya', array(
            'id' => '4',
            'name' => "Milliy tiklanish DP",
        ));

        $this->insert('partiya', array(
            'id' => '5',
            'name' => "O'Z LiDeP",
        ));

        $this->insert('partiya', array(
            'id' => '6',
            'name' => "Ekologiya",
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partiya}}');
        echo "m220610_052036_partiya cannot be reverted.\n";

        return false;
    }
}
