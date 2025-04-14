<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification_role}}`.
 */
class m220205_063827_create_notification_role_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (Yii::$app->db->getTableSchema('{{%notification_role}}', true) != null) {
            $this->dropTable('{{%notification_role}}');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%notification_role}}', [
            'id' => $this->primaryKey(),
            'notification_id' => $this->integer()->notNull(),

            'role' => $this->string(33),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('nrn_notificarion_role_notification_id_mk', 'notification_role', 'notification_id', 'notification', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('nrn_notificarion_role_notification_id_mk', 'notification_role');
        $this->dropTable('{{%notification_role}}');
    }
}
