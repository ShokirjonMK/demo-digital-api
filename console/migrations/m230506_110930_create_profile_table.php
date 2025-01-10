<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%profile}}`.
 */
class m230506_110930_create_profile_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'profile';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('profile');
        }

        $this->createTable('{{%profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'checked' => $this->tinyInteger()->defaultValue(0),
            'checked_full' => $this->tinyInteger()->defaultValue(0),
            'image' => $this->string(255)->null(),

            'first_name' => $this->string(255)->null(),
            'last_name' => $this->string(255)->null(),
            'middle_name' => $this->string(255)->null(),
            'passport_serial' => $this->string()->null(),
            'passport_number' => $this->string()->null(),
            'passport_pin' => $this->string(15)->null(),
            'passport_issued_date' => $this->date()->null(),
            'passport_given_date' => $this->date()->null(),
            'passport_given_by' => $this->string(255)->null(),
            'birthday' => $this->date()->null(),
            'gender' => $this->tinyInteger(1)->null(),
            'phone' => $this->string(50)->null(),
            'phone_secondary' => $this->string(50)->null(),
            'passport_file' => $this->string(255)->null(),
            'all_file' => $this->string(255)->null(),

            'countries_id' => $this->integer()->null(),
            'region_id' => $this->integer()->null(),
            'area_id' => $this->integer()->null(),

            'permanent_countries_id' => $this->integer()->null(),
            'permanent_region_id' => $this->integer()->null(),
            'permanent_area_id' => $this->integer()->null(),


            'permanent_address' => $this->string()->null(),
            'address' => $this->text()->null(),
            'description' => $this->text()->null(),

            'is_foreign' => $this->tinyInteger(1)->null(),

            'citizenship_id' => $this->integer()->null()->comment('citizenship_id fuqarolik turi'),
            'nationality_id' => $this->integer()->null()->comment('millati id'),
            'telegram_chat_id' => $this->integer()->null(),
            'diploma_type_id' => $this->integer()->null()->comment('diploma_type'),
            'degree_id' => $this->integer()->null()->comment('darajasi id'),
            'academic_degree_id' => $this->integer()->null()->comment('academic_degree id'),
            'degree_info_id' => $this->integer()->null()->comment('degree_info id'),
            'partiya_id' => $this->integer()->null()->comment('partiya id'),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('mk_profile_table_users_table', 'profile', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_profile_table_countries_table', 'profile', 'countries_id', 'countries', 'id');
        $this->addForeignKey('mk_profile_table_region_table', 'profile', 'region_id', 'region', 'id');
        $this->addForeignKey('mk_profile_table_area_table', 'profile', 'area_id', 'area', 'id');
        $this->addForeignKey('mk_profile_table_nationality_table', 'profile', 'nationality_id', 'nationality', 'id');
        $this->addForeignKey('ui_profile_table_citizenships_table', 'profile', 'citizenship_id', 'citizenship', 'id');

        $this->addForeignKey('mk_profile_table_permanent_countries_table', 'profile', 'permanent_countries_id', 'countries', 'id');
        $this->addForeignKey('mk_profile_table_permanent_region_table', 'profile', 'permanent_region_id', 'region', 'id');
        $this->addForeignKey('mk_profile_table_permanent_area_table', 'profile', 'permanent_area_id', 'area', 'id');


        $this->insert('{{%profile}}', [
            'user_id' => 1,
            'first_name' => 'ShokirjonMK',
            'last_name' => 'ShokirjonMK_uz',
            'middle_name' => 'ShokirjonMKuz',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 2,
            'first_name' => 'Ik.',
            'last_name' => 'Anvarovich',
            'middle_name' => 'Anvarjon o\'g\'li',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 3,
            'first_name' => 'Ahror',
            'last_name' => 'Ahror',
            'middle_name' => 'Ahror',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 4,
            'first_name' => 'Azizxon',
            'last_name' => 'Azizxon',
            'middle_name' => 'Azizxon',
            'created_at' => time(),
            'updated_at' => time(),
        ]);




    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_profile_table_users_table', 'profile');
        $this->dropForeignKey('mk_profile_table_countries_table', 'profile');
        $this->dropForeignKey('mk_profile_table_region_table', 'profile');
        $this->dropForeignKey('mk_profile_table_area_table', 'profile');
        $this->dropForeignKey('mk_profile_table_nationality_table', 'profile');
        $this->dropForeignKey('ui_profile_table_citizenships_table', 'profile');

        $this->dropForeignKey('mk_profile_table_permanent_country_table', 'profile');
        $this->dropForeignKey('mk_profile_table_permanent_region_table', 'profile');
        $this->dropForeignKey('mk_profile_table_permanent_area_table', 'profile');
        $this->dropTable('{{%profile}}');
    }
}
