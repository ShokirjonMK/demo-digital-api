<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%teacher_access}}`.
 */
class m230513_074605_create_teacher_access_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'teacher_access';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('teacher_access');
        }

        $this->createTable('{{%teacher_access}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'is_lecture' => $this->tinyInteger(1)->defaultValue(0),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_teacher_access_table_users_table', 'teacher_access', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_teacher_access_table_subject_table', 'teacher_access', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_teacher_access_table_languages_table', 'teacher_access', 'language_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_teacher_access_table_users_table', 'teacher_access');
        $this->dropForeignKey('mk_teacher_access_table_subject_table', 'teacher_access');
        $this->dropForeignKey('mk_teacher_access_table_languages_table', 'teacher_access');
        $this->dropTable('{{%teacher_access}}');
    }
}
