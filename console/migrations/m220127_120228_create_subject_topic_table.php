<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_topic}}`.
 */
class m220127_120228_create_subject_topic_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (Yii::$app->db->getTableSchema('{{%subject_topic}}', true) != null) {
            $this->dropTable('{{%subject_topic}}');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%subject_topic}}', [
            'id' => $this->primaryKey(),
            'name' => $this->text()->notNull(),
            'hours' => $this->integer()->defaultValue(0),
            'subject_id' => $this->integer()->notNull(),
            'lang_id' => $this->integer()->notNull(),
            'description' => $this->text()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('sts_subject_topic_subject_id_mk', 'subject_topic', 'subject_id', 'subject', 'id');
        $this->addForeignKey('stl_subject_topic_lang_id_mk', 'subject_topic', 'lang_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sts_subject_topic_subject_id_mk', 'subject_topic');
        $this->dropForeignKey('stl_subject_topic_lang_id_mk', 'subject_topic');

        $this->dropTable('{{%subject_topic}}');
    }
}
