<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%survey_question_info}}`.
 */
class m220519_102901_create_survey_question_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'survey_question_info';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('survey_question_info');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%survey_question_info}}', [

            'id' => $this->primaryKey(),
            'survey_question_id' => $this->integer()->notNull(),
            'lang' => $this->string(2)->notNull(),
            'question' => $this->text()->null(),
            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('sqisq_survey_question_info_survey_question', 'survey_question_info', 'survey_question_id', 'survey_question', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sqisq_survey_question_info_survey_question', 'survey_question_info');
        $this->dropTable('{{%survey_question_info}}');
    }
}
