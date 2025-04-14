<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%survey_question}}`.
 */
class m220519_102213_create_survey_question_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'survey_question';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('survey_question');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%survey_question}}', [
            'id' => $this->primaryKey(),
            'min' => $this->integer()->notNull()->defaultValue(0),
            'max' => $this->integer()->notNull()->defaultValue(10),
            'type' => $this->tinyInteger(1)->defaultValue(1)->comment('1-ball kiriteykon, 2-yozeykon'),

            // savol matni infoda

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%survey_question}}');
    }
}
