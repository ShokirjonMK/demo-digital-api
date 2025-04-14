<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$routes = include __DIR__ . '/routes.php';
$host_name = array_value($params, 'domain_name', get_host());

$main_config = array(
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'params' => $params,
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache', // You can choose other cache classes like 'yii\caching\DbCache'
            // Other cache configuration options...
        ],
        // Other components...

        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
            ]
        ],
        'response' => [
            'format' =>  \yii\web\Response::FORMAT_JSON
        ],
        'telegram' => [
            'class' => 'aki\telegram\Telegram',
            // 'webHook' => true,
            // 'webHook' => [
            //     'url' => 'https://api-digital.tsul.uz/bot',
            // ],
            'botToken' => '5268005235:AAHo7-xdDMnGcfGL2vdMrzXMWhRfGa88_yk',
        ],
        /*  'telegram' => [
            'class' => 'aki\telegram\Telegram',
            'webHook' => [
                'url' => 'https://api-digital.tsul.uz/bot', // Your webhook URL
                // Add other webhook options if needed
            ],
            'botToken' => '5268005235:AAHo7-xdDMnGcfGL2vdMrzXMWhRfGa88_yk',
            'commands' => [
                // Your commands here
            ],
        ], */

        'user' => [
            'identityClass' => 'common\models\User',
            'enableSession' => false,
            'loginUrl' => null,
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => 'identity-user',
                'path' => '/',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => $routes,
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@api/translations',
                    'fileMap' => [
                        'app' => 'app.php',
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
);

return check_app_config_files($main_config);
