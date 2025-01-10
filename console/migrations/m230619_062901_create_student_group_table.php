<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_group}}`.
 */
class m230619_062901_create_student_group_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'student_group';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_group');
        }

        $this->createTable('{{%student_group}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_student_group_table_student_table', 'student_group', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_student_group_table_group_table', 'student_group', 'group_id', 'group', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_student_group_table_student_table', 'student_group');
        $this->dropForeignKey('mk_student_group_table_group_table', 'student_group');
        $this->dropTable('{{%student_group}}');
    }
}
