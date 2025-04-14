<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_appeal}}`.
 */
class m240909_043319_create_user_appeal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'user_appeal';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('user_appeal');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_appeal}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('ID of the user who created the record'),

            'main' => $this->tinyInteger(1)->defaultValue(1)->comment('student emas 1 , 0 student'),
            'type' => $this->integer()->null(),
            'type_mini' => $this->integer()->null(),

            'text' => $this->text()->null(),
            'answer_text' => $this->text()->null(),
            'file' => $this->string()->null(),
            'answer_file' => $this->string()->null(),

            'date' => $this->date()->null(),
            'para_id' => $this->integer()->null(),
            'description' => $this->text()->null(),

            'order' => $this->tinyInteger(1)->defaultValue(1)->comment('Order of the item'),
            'status' => $this->tinyInteger(1)->defaultValue(1)->comment('Status of the item (1 = active, 0 = inactive)'),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0)->comment('Is the item deleted (0 = no, 1 = yes)'),
            'created_at' => $this->integer()->notNull()->defaultValue(0)->comment('Creation timestamp'),
            'updated_at' => $this->integer()->notNull()->defaultValue(0)->comment('Update timestamp'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who created the record'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who last updated the record'),
            'archived' => $this->integer()->notNull()->defaultValue(0)->comment('Is the item archived (0 = no, 1 = yes)'),
        ], $tableOptions);

        $this->addForeignKey('fk-user_appeal-user_id', '{{%user_appeal}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_appeal-user_id', '{{%user_appeal}}');
        $this->dropTable('{{%user_appeal}}');
    }
}
