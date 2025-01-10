<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_topic_permission}}`.
 */
class m230806_050143_create_student_topic_permission_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'student_topic_permission';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_topic_permission');
        }

        $this->createTable('{{%student_topic_permission}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'topic_id' => $this->integer()->notNull(),
            'attempts_count' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->integer()->defaultValue(0),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_student_topic_permission_table_student_table', 'student_topic_permission', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_student_topic_permission_table_user_table', 'student_topic_permission', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_student_topic_permission_table_subject_topic_table', 'student_topic_permission', 'topic_id', 'subject_topic', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_student_topic_permission_table_student_table', 'student_topic_permission');
        $this->dropForeignKey('mk_student_topic_permission_table_user_table', 'student_topic_permission');
        $this->dropForeignKey('mk_student_topic_permission_table_subject_topic_table', 'student_topic_permission');
        $this->dropTable('{{%student_topic_permission}}');
    }
}
