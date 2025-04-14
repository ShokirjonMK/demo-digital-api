<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tourniquet_absent}}`.
 */
class m230310_100340_create_tourniquet_absent_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'tourniquet_absent';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('tourniquet_absent');
        }

        $this->createTable('{{%tourniquet_absent}}', [
            'id' => $this->primaryKey(),

            'user_id' => $this->integer()->null(),
            'roles' => $this->string(255),
            'passport_pin' => $this->string(255)->notNull(),
            'date' => $this->date()->null(),
            'date_time' => $this->dateTime()->null(),
            'date_out' => $this->time()->null(),
            'date_in' => $this->time()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
        $this->createIndex('idx-tourniquet_absent-passport_pin', 'tourniquet_absent', 'passport_pin');
        $this->addForeignKey('excel_atendens_profile_id', 'tourniquet_absent', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tourniquet_absent}}');
    }
}
