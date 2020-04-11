<?php
use yii\db\Connection;

return [
    'class' => Connection::class,
    'dsn' => "mysql:host=".env('DB_HOST').";dbname=".env('DB_NAME').";port=".env('DB_PORT').";",
    'username' => env('DB_USER'),
    'password' => env('DB_PASSWORD'),
    'charset' => 'utf8',

    'enableSchemaCache' => (env('DB_ENABLE_CACHE') === 'true'),
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
