<?php
return [
    'class' => '\yii\mongodb\Connection',
    'dsn' => "mongodb://".env('MONGODB_HOST').":".env('MONGODB_PORT')."/" . env('MONGODB_DB_NAME'),
//    'options' => [
//        "username" => env('MONGODB_USERNAME'),
//        "password" => env('MONGODB_PASSWORD')
//    ],
    'enableLogging' => true, // enable logging
    'enableProfiling' => true,
];