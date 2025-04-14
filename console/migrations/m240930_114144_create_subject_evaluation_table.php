<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_evaluation}}`.
 */
class m240930_114144_create_subject_evaluation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'subject_evaluation';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject_evaluation');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%subject_evaluation}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),

            'control_submission' => $this->text()->null(),
            'control_assessment' => $this->text()->null(),
            'final_submission' => $this->text()->null(),
            'final_assessment' => $this->text()->null(),


            'control_submission_ru' => $this->text()->null(),
            'control_assessment_ru' => $this->text()->null(),
            'final_submission_ru' => $this->text()->null(),
            'final_assessment_ru' => $this->text()->null(),


            'control_submission_en' => $this->text()->null(),
            'control_assessment_en' => $this->text()->null(),
            'final_submission_en' => $this->text()->null(),
            'final_assessment_en' => $this->text()->null(),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),

            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('fk-subject_evaluation-subject_id', '{{%subject_evaluation}}', 'subject_id', '{{%subject}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-subject_evaluation-subject_id', '{{%subject_evaluation}}');

        $this->dropTable('{{%subject_evaluation}}');
    }
}
