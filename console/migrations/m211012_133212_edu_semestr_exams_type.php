<?php

use yii\db\Migration;

/**
 * Class m211012_133212_edu_semestr_exams_type
 */
class m211012_133212_edu_semestr_exams_type extends Migration
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
        $this->createTable('edu_semestr_exams_type', [
            'id' => $this->primaryKey(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'exams_type_id' => $this->integer()->notNull(),
            'max-ball' => $this->integer()->notNull(),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('se_edu_semestr_exams_type_edu_subject_id', 'edu_semestr_exams_type', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('xe_edu_semestr_exams_type_exams_id', 'edu_semestr_exams_type', 'exams_type_id', 'exams_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('se_edu_semestr_exams_type_edu_subject_id', 'edu_semestr_exams_type');
        $this->dropForeignKey('xe_edu_semestr_exams_type_exams_id', 'edu_semestr_exams_type');
        $this->dropTable('edu_semestr_exams_type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_133212_edu_semestr_exams_type cannot be reverted.\n";

        return false;
    }
    */
}
