<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%final_exam_confirm}}`.
 */
class m240118_072027_create_final_exam_confirm_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'final_exam_confirm';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('final_exam_confirm');
        }

        $this->createTable('{{%final_exam_confirm}}', [
            'id' => $this->primaryKey(),
            'final_exam_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'role_name' => $this->string(255)->null(),
            'date' => $this->integer()->null(),
            'type' => $this->integer()->defaultValue(1),
            'qr_code' =>  $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_final_exam_confirm_table_final_exam_table', 'final_exam_confirm', 'final_exam_id', 'final_exam', 'id');
        $this->addForeignKey('mk_final_exam_confirm_table_user_table', 'final_exam_confirm', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%final_exam_confirm}}');
    }
}
