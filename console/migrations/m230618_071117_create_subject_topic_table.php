<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_topic}}`.
 */
class m230618_071117_create_subject_topic_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'subject_topic';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject_topic');
        }

        $this->createTable('{{%subject_topic}}', [
            'id' => $this->primaryKey(),
            'teacher_access_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->null(),
            'lang_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->null()->comment('Fan turlari boyicha topic uchun'),
            'name' => $this->text()->notNull(),
            'description' => $this->text()->null(),
            'hours' => $this->integer()->notNull()->defaultValue(0),

            'allotted_time' => $this->integer()->null(),
            'attempts_count' => $this->integer()->null(),
            'duration_reading_time' => $this->integer()->null(),
            'test_count' => $this->integer()->defaultValue(0),
            'min_percentage' => $this->float()->defaultValue(0),

            'order'=> $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ],$tableOptions);
        $this->addForeignKey('mk_subject_topic_table_teacher_access_table', 'subject_topic', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('mk_subject_topic_table_parent_table', 'subject_topic', 'parent_id', 'subject_topic', 'id');
        $this->addForeignKey('mk_subject_topic_table_subject_table', 'subject_topic', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_subject_topic_table_lang_table', 'subject_topic', 'lang_id', 'languages', 'id');
        $this->addForeignKey('mk_subject_topic_table_subject_category_table', 'subject_topic', 'subject_category_id', 'subject_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_subject_topic_table_teacher_access_table', 'subject_topic');
        $this->dropForeignKey('mk_subject_topic_table_parent_table', 'subject_topic');
        $this->dropForeignKey('mk_subject_topic_table_subject_table', 'subject_topic');
        $this->dropForeignKey('mk_subject_topic_table_lang_table', 'subject_topic');
        $this->dropForeignKey('mk_subject_topic_table_subject_category_table', 'subject_topic');
        $this->dropTable('{{%subject_topic}}');
    }
}
