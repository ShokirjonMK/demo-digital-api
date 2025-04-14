<?php

use yii\db\Migration;

/**
 * Class m220610_052950_diploma_type
 */
class m220610_052950_diploma_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'diploma_type';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('diploma_type');
        }

        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%diploma_type}}', [
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

        $this->insert('diploma_type', array(
            'id' => '1',
            'name' => "Bakalavr",
        ));

        $this->insert('diploma_type', array(
            'id' => '2',
            'name' => "Magistr",
        ));

        $this->insert('diploma_type', array(
            'id' => '3',
            'name' => "PhD (fan nomzodi)",
        ));

        $this->insert('diploma_type', array(
            'id' => '4',
            'name' => "DSc (fan doktori)",
        ));

        $this->insert('diploma_type', array(
            'id' => '5',
            'name' => "Mutaxassislik diplomi",
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%diploma_type}}');
        echo "m220610_052950_diploma_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220610_052950_diploma_type cannot be reverted.\n";

        return false;
    }
    */
}
