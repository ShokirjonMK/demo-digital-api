<?php

return array (
  'app_id' => 'HYEIC-cf949b7e39af3df21af3defce7a9e7aa',
  'site_master_pass' => 388628,
  'domain_name' => 'localhost.loc',
  'api_url' => 'http://api.localhost.loc/',
  'assets_url' => 'http://assets.localhost.loc/',
  'admin_url' => 'http://admin.localhost.loc/',
  'site_url' => 'http://localhost.loc/',
  'local_cache' => false,
  'theme_force_copy' => false,
  'redis' => 
  array (
    'active' => false,
    'prefix' => 'mywebsite',
    'password' => '',
    'secret_key' => '2xNDR9stXUjmryc4OFYbkwguBHaSKdALTqpC35vhznGI81EZio',
    'secret_iv' => 'Vdkn2w4YQU0POo7JXWc9IATMSFeE6sDHzZBf8C5tLbmgvuxiGK',
    'config' => 
    array (
      'host' => '127.0.0.1',
      'port' => '6379',
      'scheme' => 'tcp',
    ),
  ),
  'database' => 
  array (
    'db' => 
    array (
      'class' => 'yii\\db\\Connection',
      // 'dsn' => 'mysql:host=localhost;dbname=digital_mk1',
      'dsn' => 'pgsql:host=localhost;dbname=mk_db_pg',

      'username' => 'mk_digital_user',
      'password' => 'mkpasposgres',
      'charset' => 'utf8',
      // 'attributes' => 
      // array (
      //   1002 => 'SET sql_mode=(SELECT REPLACE(@@sql_mode,\'ONLY_FULL_GROUP_BY\',\'\'));',
      // ),
      'schemaMap' => array(
        'pgsql' => array(
          'class' => 'yii\db\pgsql\Schema',
          'defaultSchema' => 'public' // Default schema for PostgreSQL
        )
      ),
    ),
  ),
  'mailer' => 
  array (
    'class' => 'yii\\swiftmailer\\Mailer',
    'useFileTransport' => true,
  ),
  'adminEmail' => 'mkshokirjon@gmail.com',
  'infoEmail' => 'info@domain.com',
  'supportEmail' => 'support@domain.com',
  'senderEmail' => 'noreply@domain.com',
  'senderName' => 'MY WEBSITE',
  'mkStatusLogging' => false,
);
