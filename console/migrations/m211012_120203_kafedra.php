<?php

use yii\db\Migration;

/**
 * Class m211012_120203_kafedra
 */
class m211012_120203_kafedra extends Migration
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
        $this->createTable('kafedra', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(255)->notNull(),
            'direction_id'=>$this->integer()->notNull(),
            'faculty_id'=>$this->integer()->notNull(),


            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('dk_kafedra_direction_id','kafedra','direction_id','direction','id');
        $this->addForeignKey('fk_kafedra_faculty_id','kafedra','faculty_id','faculty','id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('dk_kafedra_direction_id','kafedra');
        $this->dropForeignKey('fk_kafedra_faculty_id','kafedra');
        $this->dropTable('kafedra');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_120203_kafedra cannot be reverted.\n";

        return false;
    }
    */
}
