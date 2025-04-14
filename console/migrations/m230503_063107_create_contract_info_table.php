<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%contract_info}}`.
 */
class m230503_063107_create_contract_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'contract_info';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('contract_info');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%contract_info}}', [
            'id' => $this->primaryKey(),

            'student_id' => $this->integer()->null(),
            'uzasbo_id' => $this->integer()->null(),
            'passport_pin' => $this->string(255)->null(),
            'contract_number' => $this->string(255)->null(),
            'scholarship_type' => $this->string(255)->null(),
            'contract_price' => $this->double(),
            'contract_price_half' => $this->double(),
            'reception_type' => $this->string(255)->null(),
            'order_class' => $this->string(255)->null(),
            'order_enter' => $this->string(255)->null(),
            'order_no_class' => $this->string(255)->null(),
            'order_fire' => $this->string(255)->null(),
            'order_edu_holiday' => $this->string(255)->null(),
            'order_change_edu_form' => $this->string(255)->null(),
            'debt_begin' => $this->double()->defaultValue(0),
            'overpayment' => $this->double()->defaultValue(0),
            'must_pay_this_year' => $this->double()->defaultValue(0),
            'paid_this_year' => $this->double()->defaultValue(0),
            'payment_percent' => $this->double()->defaultValue(0),
            'debt_or_overpayment' => $this->double()->defaultValue(0),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);
        $this->addForeignKey('mk_contract_info_student_id', 'contract_info', 'student_id', 'student', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_contract_info_student_id', 'contract_info');
        $this->dropTable('{{%contract_info}}');
    }
}
