<?php

namespace app\modules\websocket\handlers;

use Yii;
use yii\base\BaseObject;

/**
 * Class BaseHandler
 * @package app\modules\websocket\handlers
 *
 * @property-write mixed $data
 */
class BaseHandler extends BaseObject
{
    /**
     * @param $handlerClass
     * @return BaseHandler
     */
    public static function getHandler($handlerClass)
    {
        $class = __NAMESPACE__ .'\\' . ucfirst(strtolower($handlerClass)) . 'Handler';
        if (!class_exists($class)) {
            return new self();
        }

        return new $class();
    }

    /**
     * @param $data
     * @return static
     */
    public function setData($data)
    {
        if (!empty($data)) {
            Yii::configure($this, $data);
        }
        $this->init();

        return $this;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        return true;
    }
}