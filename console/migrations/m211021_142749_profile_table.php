<?php

use yii\db\Migration;

/**
 * Class m211021_142749_profile_table
 */
class m211021_142749_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'profile';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('profile');
        }


        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('profile', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'telegram_chat_id' => $this->integer()->Null(),
            'last_in' => $this->dateTime()->Null(),

            'image' => $this->string(255)->Null(),
            'phone' => $this->string(50)->Null(),
            'phone_secondary' => $this->string(50)->Null(),
            'is_foreign' => $this->integer()->Null(),
            'last_name' => $this->string(255)->Null(),
            'first_name' => $this->string(255)->Null(),
            'middle_name' => $this->string(255)->Null(),
            'passport_seria' => $this->string(255)->Null(),
            'passport_number' => $this->string(255)->Null(),
            'passport_pin' => $this->string(255)->Null(),
            'birthday' => $this->date()->Null(),
            'passport_file' => $this->string(255)->Null(),
            'country_id' => $this->integer()->Null(),
            'region_id' => $this->integer()->Null(),
            'area_id' => $this->integer()->Null(),
            'address' => $this->string(255)->Null(),
            'gender' => $this->integer()->Null(),
            'passport_given_date' => $this->date()->Null(),
            'passport_issued_date' => $this->date()->Null(),
            'passport_given_by' => $this->string(255)->Null(),
            'permanent_country_id' => $this->integer()->Null(),
            'permanent_region_id' => $this->integer()->Null(),
            'permanent_area_id' => $this->integer()->Null(),
            'permanent_address' => $this->string(255)->Null(),

            'checked' => $this->integer()->notNull()->defaultValue(0),
            'checked_full' => $this->integer()->notNull()->defaultValue(0),

            'has_disability' => $this->integer()->notNull()->defaultValue(0)->comment("nogirognligi bormi"),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('up_profile_user_id', 'profile', 'user_id', 'users', 'id');
        $this->addForeignKey('cp_profile_country_id', 'profile', 'country_id', 'countries', 'id');
        $this->addForeignKey('rp_profile_region_id', 'profile', 'region_id', 'region', 'id');
        $this->addForeignKey('ap_profile_area_id', 'profile', 'area_id', 'area', 'id');

        $this->addForeignKey('cp_profile_permanent_country_id', 'profile', 'permanent_country_id', 'countries', 'id');
        $this->addForeignKey('rp_profile_permanent_region_id', 'profile', 'permanent_region_id', 'region', 'id');
        $this->addForeignKey('ap_profile_permanent_area_id', 'profile', 'permanent_area_id', 'area', 'id');


        $this->insert('{{%profile}}', [
            'user_id' => 1,
            'first_name' => "Shokirjon MK",
            'last_name' => "Developer",
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 2,
            'first_name' => "Super",
            'last_name' => "Admin",
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 3,
            'first_name' => "Prof",
            'last_name' => "Dev",
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 4,
            'first_name' => "Black",
            'last_name' => "Moon",
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('up_profile_user_id', 'profile');
        $this->dropForeignKey('cp_profile_country_id', 'profile');
        $this->dropForeignKey('rp_profile_region_id', 'profile');
        $this->dropForeignKey('ap_profile_area_id', 'profile');

        $this->dropForeignKey('cp_profile_permanent_country_id', 'profile');
        $this->dropForeignKey('rp_profile_permanent_region_id', 'profile');
        $this->dropForeignKey('ap_profile_permanent_area_id', 'profile');
        $this->dropTable('profile');
    }
}
