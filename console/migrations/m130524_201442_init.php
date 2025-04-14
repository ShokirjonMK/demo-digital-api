<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // Optimized for modern MySQL with utf8mb4 and improved performance settings
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB ROW_FORMAT=DYNAMIC';
        }

        // Users table
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'username' => $this->string(64)->notNull()->unique()->comment('Username'),
            'attendence' => $this->tinyInteger()->defaultValue(1)->comment('Attendance'),
            'auth_key' => $this->string(32)->notNull()->comment('Auth key'),
            'password_hash' => $this->string(60)->notNull()->comment('Password hash'),
            'password_reset_token' => $this->string(64)->unique()->comment('Password reset token'),
            'verification_token' => $this->string(64)->comment('Verification token'),
            'access_token' => $this->string(64)->comment('Access token'),
            'access_token_time' => $this->integer()->comment('Access token time'),
            'email' => $this->string(128)->comment('Email'),
            'template' => $this->string(64)->comment('Template'),
            'layout' => $this->string(64)->comment('Layout'),
            'view' => $this->string(64)->comment('View'),
            'meta' => $this->json()->comment('Meta'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('Status'),
            'deleted' => $this->boolean()->notNull()->defaultValue(0)->comment('Deleted'),
            'cacheable' => $this->boolean()->notNull()->defaultValue(0)->comment('Cacheable'),
            'searchable' => $this->boolean()->notNull()->defaultValue(0)->comment('Searchable'),
            'created_at' => $this->integer()->notNull()->comment('Created at'),
            'updated_at' => $this->integer()->notNull()->comment('Updated at'),
            'created_by' => $this->integer()->comment('Created by'),
            'updated_by' => $this->integer()->comment('Updated by'),
            'is_changed' => $this->boolean()->defaultValue(0)->comment('Is changed'),
            'status_n' => $this->tinyInteger()->comment('Status N'),
            'telegram_chat_id' => $this->bigInteger()->comment('Telegram chat ID'),
            'lang' => $this->char(2)->comment('Language'),
        ], $tableOptions);

        // Add indexes for users table
        $this->createIndex('idx_users_status', '{{%users}}', 'status');
        $this->createIndex('idx_users_email', '{{%users}}', 'email');
        $this->createIndex('idx_users_created_at', '{{%users}}', 'created_at');
        $this->createIndex('idx_users_deleted', '{{%users}}', 'deleted');

        // Profile table
        $this->createTable('{{%profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'checked' => $this->boolean()->defaultValue(0),
            'checked_full' => $this->boolean()->defaultValue(0),
            'image' => $this->string(255),
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
            'passport_file' => $this->string(255),
            'country_id' => $this->integer(),
            'is_foreign' => $this->boolean(),
            'region_id' => $this->integer(),
            'area_id' => $this->integer(),
            'address' => $this->string(255),
            'gender' => $this->tinyInteger(),
            'permanent_country_id' => $this->integer(),
            'permanent_region_id' => $this->integer(),
            'permanent_area_id' => $this->integer(),
            'permanent_address' => $this->string(255),
            'order' => $this->tinyInteger(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'description' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(0),
            'citizenship_id' => $this->integer()->defaultValue(1)->comment('citizenship_id fuqarolik turi'),
            'nationality_id' => $this->integer()->comment('millati id'),
            'telegram_chat_id' => $this->integer(),
            'diploma_type_id' => $this->integer()->comment('diploma_type'),
            'degree_id' => $this->integer()->comment('darajasi id'),
            'academic_degree_id' => $this->integer()->comment('academic_degree id'),
            'degree_info_id' => $this->integer()->comment('degree_info id'),
            'partiya_id' => $this->integer()->comment('partiya id'),
            'has_disability' => $this->boolean()->defaultValue(0)->comment('nogironligi'),
            'social_protection' => $this->boolean()->defaultValue(0)->comment('ijtimoiy himoya reestri'),
            'last_in' => $this->dateTime(),
            'house_of_kindness' => $this->boolean()->defaultValue(0),
            'underprivileged' => $this->boolean()->defaultValue(0),
            'orcid' => $this->string(64),
            'turniket_id' => $this->integer()->comment('turniketdan qaytgan ID'),
            'turniket_status' => $this->boolean()->defaultValue(0)->comment('turniketga biriktirilganligi'),
        ], $tableOptions);

        // Add indexes for profile table
        $this->createIndex('idx_profile_user', '{{%profile}}', 'user_id');
        $this->createIndex('idx_profile_passport', '{{%profile}}', ['passport_seria', 'passport_number']);
        $this->createIndex('idx_profile_name', '{{%profile}}', ['last_name', 'first_name']);
        $this->createIndex('idx_profile_phone', '{{%profile}}', 'phone');
        $this->createIndex('idx_profile_status', '{{%profile}}', ['status', 'is_deleted']);

        // Optimized foreign keys with proper cascade actions
        $this->addForeignKey('fk_profile_area', '{{%profile}}', 'area_id', '{{%area}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_profile_permanent_area', '{{%profile}}', 'permanent_area_id', '{{%area}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_profile_country', '{{%profile}}', 'country_id', '{{%countries}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_profile_permanent_country', '{{%profile}}', 'permanent_country_id', '{{%countries}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_profile_citizenship', '{{%profile}}', 'citizenship_id', '{{%citizenship}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_profile_permanent_region', '{{%profile}}', 'permanent_region_id', '{{%region}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_profile_region', '{{%profile}}', 'region_id', '{{%region}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_profile_user', '{{%profile}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');

        // Employee table with optimized structure
        $this->createTable('{{%employee}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'department_id' => $this->integer(),
            'job_id' => $this->integer(),
            'inps' => $this->string(64),
            'scientific_work' => $this->text(),
            'languages' => $this->string(255),
            'lang_certs' => $this->string(255),
            'rate' => $this->decimal(10, 2),
            'rank_id' => $this->integer(),
            'science_degree_id' => $this->integer(),
            'scientific_title_id' => $this->integer(),
            'special_title_id' => $this->integer(),
            'reception_time' => $this->string(64),
            'out_staff' => $this->boolean(),
            'basic_job' => $this->boolean(),
            'is_convicted' => $this->boolean(),
            'party_membership' => $this->boolean(),
            'awords' => $this->string(255),
            'depuities' => $this->string(255),
            'military_rank' => $this->string(64),
            'disability_group' => $this->tinyInteger(1),
            'family_status' => $this->tinyInteger(1),
            'children' => $this->string(255),
            'other_info' => $this->text(),
        ], $tableOptions);

        // Add indexes for employee table
        $this->createIndex('idx_employee_user', '{{%employee}}', 'user_id');
        $this->createIndex('idx_employee_department', '{{%employee}}', 'department_id');
        $this->createIndex('idx_employee_job', '{{%employee}}', 'job_id');

        // Student table with optimized structure
        $this->createTable('{{%student}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'department_id' => $this->integer(),
            'education_direction_id' => $this->integer(),
            'basis_of_learning' => $this->tinyInteger(),
            'education_type' => $this->tinyInteger(),
            'diploma_number' => $this->string(64),
            'diploma_date' => $this->date(),
            'type_of_residence' => $this->tinyInteger(1),
            'landlord_info' => $this->text(),
            'student_live_with' => $this->text(),
            'other_info' => $this->text(),
        ], $tableOptions);

        // Add indexes for student table
        $this->createIndex('idx_student_user', '{{%student}}', 'user_id');
        $this->createIndex('idx_student_department', '{{%student}}', 'department_id');
        $this->createIndex('idx_student_education', '{{%student}}', 'education_direction_id');

        // Add foreign keys for employee and student tables
        $this->addForeignKey('fk_employee_user', '{{%employee}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_student_user', '{{%student}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');

        // inserting data

        $this->insert('{{%users}}', [
            'username' => 'ShokirjonMK',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("12300123"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'mk@mk.com',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'suadmin',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("susu1221"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'suadmin@tsul.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'professor',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("prof007"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'admin@tsul.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'blackmoon',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("blackmoonuz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'blackmoonuz@mail.ru',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function down()
    {
        // Drop foreign keys from profile table
        $this->dropForeignKey('fk_profile_area', '{{%profile}}');
        $this->dropForeignKey('fk_profile_permanent_area', '{{%profile}}');
        $this->dropForeignKey('fk_profile_country', '{{%profile}}');
        $this->dropForeignKey('fk_profile_permanent_country', '{{%profile}}');
        $this->dropForeignKey('fk_profile_citizenship', '{{%profile}}');
        $this->dropForeignKey('fk_profile_permanent_region', '{{%profile}}');
        $this->dropForeignKey('fk_profile_region', '{{%profile}}');
        $this->dropForeignKey('fk_profile_user', '{{%profile}}');

        // Drop foreign keys from employee and student tables
        $this->dropForeignKey('fk_employee_user', '{{%employee}}');
        $this->dropForeignKey('fk_student_user', '{{%student}}');

        // Drop indexes from users table
        $this->dropIndex('idx_users_status', '{{%users}}');
        $this->dropIndex('idx_users_email', '{{%users}}');
        $this->dropIndex('idx_users_created_at', '{{%users}}');
        $this->dropIndex('idx_users_deleted', '{{%users}}');

        // Drop indexes from profile table
        $this->dropIndex('idx_profile_user', '{{%profile}}');
        $this->dropIndex('idx_profile_passport', '{{%profile}}');
        $this->dropIndex('idx_profile_name', '{{%profile}}');
        $this->dropIndex('idx_profile_phone', '{{%profile}}');
        $this->dropIndex('idx_profile_status', '{{%profile}}');

        // Drop indexes from employee table
        $this->dropIndex('idx_employee_user', '{{%employee}}');
        $this->dropIndex('idx_employee_department', '{{%employee}}');
        $this->dropIndex('idx_employee_job', '{{%employee}}');

        // Drop indexes from student table
        $this->dropIndex('idx_student_user', '{{%student}}');
        $this->dropIndex('idx_student_department', '{{%student}}');
        $this->dropIndex('idx_student_education', '{{%student}}');

        // Drop tables in reverse order of creation
        $this->dropTable('{{%student}}');
        $this->dropTable('{{%employee}}');
        $this->dropTable('{{%profile}}');
        $this->dropTable('{{%users}}');

        return true;
    }
}
