<?php

namespace app\modules\admin;

use yii\base\Module;

/**
 * pro-admin module definition class
 */
class AdminModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
