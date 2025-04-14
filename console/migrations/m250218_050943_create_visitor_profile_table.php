<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%visitor_profile}}`.
 */
class m250218_050943_create_visitor_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'visitor_profile';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('visitor_profile');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%visitor_profile}}', [
            'id' => $this->primaryKey(),
            'checked_full' => $this->tinyInteger(1)->defaultValue(0),
            'image' => $this->string(255),
            'birth_place' => $this->string(255),
            'last_name' => $this->string(64),
            'first_name' => $this->string(64),
            'middle_name' => $this->string(64),
            'passport_seria' => $this->string(10),
            'passport_number' => $this->string(20),
            'passport_pin' => $this->string(20),
            'passport_given_date' => $this->date(),
            'passport_issued_date' => $this->date(),
            'passport_given_by' => $this->string(128),
            'birthday' => $this->date(),
            'phone' => $this->string(20),
            'phone_secondary' => $this->string(20),
            'citizenship_id' => $this->integer()->defaultValue(1)->comment('citizenship_id fuqarolik turi'),
            'nationality_id' => $this->integer()->comment('millati id'),

            'country_id' => $this->integer(),
            'is_foreign' => $this->boolean(),
            'region_id' => $this->integer(),
            'area_id' => $this->integer(),
            'address' => $this->string(255),
            'gender' => $this->tinyInteger(),

            'description' => $this->text(),

            'turniket_id' => $this->integer()->comment('turniketdan qaytgan ID'),
            'turniket_status' => $this->boolean()->defaultValue(0)->comment('turniketga biriktirilganligi'),

            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Status: 0-inactive, 1-active'),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),

            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_key_profile_area', '{{%profile}}', 'area_id', '{{%area}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('mk_key_profile_country', '{{%profile}}', 'country_id', '{{%countries}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('mk_key_profile_citizenship', '{{%profile}}', 'citizenship_id', '{{%citizenship}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('mk_key_profile_nationality', '{{%profile}}', 'nationality_id', '{{%nationality}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('mk_key_profile_region', '{{%profile}}', 'region_id', '{{%region}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('mk_key_profile_user', '{{%profile}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%visitor_profile}}');
    }
}
