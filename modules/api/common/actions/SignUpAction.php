<?php
namespace app\modules\api\common\actions;

use yii\rest\Action;

/**
 * Class SignUpAction
 *
 * @package app\modules\api\common\actions
 */
class SignUpAction extends Action
{
    public function run()
    {
        return $this->controller->onSuccess('Cool');
    }
}