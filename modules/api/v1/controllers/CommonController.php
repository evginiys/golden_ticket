<?php

namespace app\modules\api\v1\controllers;

use app\modules\api\common\controllers\ApiController;

/**
 * Class CommonController
 * @package app\modules\api\v1\controllers
 */
class CommonController extends ApiController
{
    /** @var array  */
    public $notNeedTokenActions = ['error'];

    /** @var string  */
    public $modelClass = '';
}