<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_topic_reference}}`.
 */
class m220812_061953_create_subject_topic_reference_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'subject_topic_reference';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject_topic_reference');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%subject_topic_reference}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),
            'subject_topic_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'teacher_access_id' => $this->integer()->null(),

            'link' => $this->string(255)->Null(),
            'name' => $this->text()->Null(),
            'start_page' => $this->integer()->Null(),
            'end_page' => $this->integer()->Null(),
            'type' => $this->tinyInteger(1)->defaultValue(1),


            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('str_subject_topic_reference_subject_id', 'subject_topic_reference', 'subject_id', 'subject', 'id');
        $this->addForeignKey('str_subject_topic_reference_subject_topic_id', 'subject_topic_reference', 'subject_topic_id', 'subject_topic', 'id');
        $this->addForeignKey('str_subject_topic_reference_user_id', 'subject_topic_reference', 'user_id', 'users', 'id');
        $this->addForeignKey('str_subject_topic_reference_teacher_access_id', 'subject_topic_reference', 'teacher_access_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('str_subject_topic_reference_subject_id', 'subject_topic_reference');
        $this->dropForeignKey('str_subject_topic_reference_subject_topic_id', 'subject_topic_reference');
        $this->dropForeignKey('str_subject_topic_reference_user_id', 'subject_topic_reference');
        $this->dropForeignKey('str_subject_topic_reference_teacher_access_id', 'subject_topic_reference');
        $this->dropTable('{{%subject_topic_reference}}');
    }
}
