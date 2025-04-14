<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_form}}`.
 */
class m220203_091543_create_edu_form_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (Yii::$app->db->getTableSchema('{{%edu_form}}', true) != null) {
            $this->dropTable('{{%edu_form}}');
        }
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%edu_form}}', [
            'id' => $this->primaryKey(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->insert('{{%edu_form}}', [
            'id' => 1,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 1, 'table_name' => 'edu_form', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 1,
            'name' => 'Kunduzgi',
            'table_name' => 'edu_form',
            'language' => 'uz',
            'description' => '1',
        ]);

        $this->insert('{{%edu_form}}', [
            'id' => 2,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 2, 'table_name' => 'edu_form', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 2,
            'name' => 'Sirtqi',
            'table_name' => 'edu_form',
            'language' => 'uz',
            'description' => '1',
        ]);

        $this->insert('{{%edu_form}}', [
            'id' => 3,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 3, 'table_name' => 'edu_form', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 3,
            'name' => 'Kechgi',
            'table_name' => 'edu_form',
            'language' => 'uz',
            'description' => '1',
        ]);
    }



    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%edu_form}}');
    }
}
