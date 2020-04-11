<?php

use yii\web\Application;

require __DIR__ . '/../vendor/autoload.php';

defined('YII_DEBUG') or define('YII_DEBUG', env('YII_DEBUG') === 'true');
defined('YII_ENV') or define('YII_ENV', env('YII_ENV'));

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new Application($config))->run();
