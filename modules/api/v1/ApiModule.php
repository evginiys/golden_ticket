<?php

namespace app\modules\api\v1;

use yii\base\Module;

/**
 * v1 module definition class
 */
class ApiModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\api\v1\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
}
