<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tabel_store}}`.
 */
class m220622_125030_create_tabel_store_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'tabel_store';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('tabel_store');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%tabel_store}}', [
            'id' => $this->primaryKey(),
            'year' => $this->integer(4)->notNull(),
            'month' => $this->integer(2)->notNull(),
            'user_access_type_id' => $this->integer()->notNull(),
            'table_id' => $this->integer()->notNull(),
            'data' => $this->json()->null()->comment('asosiy data json qilib yoziladi'),
            'is_checked' => $this->tinyInteger(1)->defaultValue(0),
            'type' => $this->tinyInteger(1)->defaultValue(1)->comment('1-birinchi yarim oylik, 2-ikkinchi yarim oylik'),
            'description' => $this->text()->null(),
            'mem' => $this->text()->null(),


            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('tsuat_tabel_store_user_access_type_id', 'tabel_store', 'user_access_type_id', 'user_access_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('tsuat_tabel_store_user_access_type_id', 'tabel_store');

        $this->dropTable('{{%tabel_store}}');
    }
}
