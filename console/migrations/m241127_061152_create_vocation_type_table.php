<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vocation_type}}`.
 */
class m241127_061152_create_vocation_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'vocation_type';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('vocation_type');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vocation_type}}', [
            'id' => $this->primaryKey(),
            // 'name' => $this->string(255)->notNull()->comment('Name of the vocation type'),
            'symbol' => $this->string(50)->notNull()->comment('Unique symbol for the vocation type'),
            'pay_type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('Pay type: 0-not pay, 1- pay'),
            // 'description' => $this->text()->null()->comment('Description of the vocation type'),


            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Status: 0-inactive, 1-active'),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),

            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        // Create index on symbol
        $this->createIndex(
            'idx-vocation_type-symbol',
            '{{%vocation_type}}',
            'symbol',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%vocation_type}}');
    }
}
