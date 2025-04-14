<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%military}}`.
 */
class m220805_101533_create_military_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'military';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('military');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%military}}', [
            'id' => $this->primaryKey(),
          
            'joy' => $this->string(255)->Null(),
            'chas_raqami' => $this->string(10)->Null(),
            'year' => $this->string(11)->Null(),
            'seria_raqami'=>$this->string(33)->Null(),
            'student_id'=>$this->integer(11)->notNull(),
            'user_id'=>$this->integer(11)->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ], $tableOptions);


//        $this->addForeignKey('sasq_survey_answer_survey_question', 'survey_answer', 'survey_question_id', 'survey_question', 'id');

        // Student
        $this->createIndex('idx_rel_student_student_id', 'military', 'student_id');
        $this->addForeignKey('fk_rel_student_student_id', 'military', 'student_id', 'student', 'id');
        // User
        $this->createIndex('idx_rel_user_user_id', 'military', 'user_id');
        $this->addForeignKey('fk_rel_user_user_id', 'military', 'user_id', 'users', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Edu_year
        $this->dropForeignKey('fk_rel_student_student_id', 'military');
        $this->dropIndex('idx_rel_student_student_id', 'military');
        // Edu_type
        $this->dropForeignKey('fk_rel_user_user_id', 'military');
        $this->dropIndex('idx_rel_user_user_id', 'military');


        $this->dropTable('{{%military}}');
    }
}
