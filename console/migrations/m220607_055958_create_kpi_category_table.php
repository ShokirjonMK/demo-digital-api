<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kpi_category}}`.
 */
class m220607_055958_create_kpi_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'kpi_category';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('kpi_category');
        }

        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%kpi_category}}', [
            'id' => $this->primaryKey(),

            'fields' => $this->json()->Null()->comment('["date", "file", "subject_category", "count_of_copyright", "link"]'),
            'max_ball' => $this->double()->defaultValue(0),
            'term' => $this->tinyInteger(1)->defaultValue(1)->comment('muddati 1-bir yil 2-olti oy'),
            'tab' => $this->tinyInteger(1)->defaultValue(1)->comment('tab raqami'),

            'type' => $this->tinyInteger(1)->defaultValue(1),
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
        $this->dropTable('{{%kpi_category}}');
    }
}
