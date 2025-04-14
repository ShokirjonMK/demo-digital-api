<?php

use yii\db\Migration;

/**
 * Class m211012_135147_teacher_access
 */
class m211012_135147_teacher_access extends Migration
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
        $this->createTable('teacher_access', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('ut_teacher_access_user_id', 'teacher_access', 'user_id', 'users', 'id');
        $this->addForeignKey('st_teacher_access_subject_id', 'teacher_access', 'subject_id', 'subject', 'id');
        $this->addForeignKey('lt_teacher_access_language_id', 'teacher_access', 'language_id', 'language', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ut_teacher_access_user_id', 'teacher_access');
        $this->dropForeignKey('st_teacher_access_subject_id', 'teacher_access');
        $this->dropForeignKey('lt_teacher_access_language_id', 'teacher_access');
        $this->dropTable('teacher_access');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_135147_teacher_access cannot be reverted.\n";

        return false;
    }
    */
}
