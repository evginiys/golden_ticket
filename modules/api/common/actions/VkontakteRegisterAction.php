<?php

namespace app\modules\api\common\actions;

use app\modules\api\common\components\CustomVk;
use Yii;
use yii\authclient\AuthAction;
use yii\base\Exception;
use yii\helpers\Url;

/**
 * Class VkontakteRegisterAction
 * @package app\modules\api\common\actions
 */
class VkontakteRegisterAction extends AuthAction
{

    public function run()
    {
        $code = Yii::$app->request->get('code');
        $client = new CustomVk();
        // return $this->auth($client);

        $request = Yii::$app->getRequest();
$client->setReturnUrl(env('VK_REDIRECT'));
        if (($error = $request->get('error')) !== null) {
            if (
                $error === 'access_denied' ||
                $error === 'user_cancelled_login' ||
                $error === 'user_cancelled_authorize'
            ) {
                // user denied error
                return $this->authCancel($client);
            }
            // request error
            $errorMessage = $request->get('error_description', $request->get('error_message'));
            if ($errorMessage === null) {
                $errorMessage = http_build_query($request->get());
            }
            throw new Exception('Auth error: ' . $errorMessage);
        }

        // Get the access_token and save them to the session.
        if (($code = $request->get('code')) !== null) {
            $token = $client->fetchAccessToken($code);
            if (!empty($token)) {
                return $this->authSuccess($client);
            }
            return $this->authCancel($client);
        }

        $url = $client->buildAuthUrl($authUrlParams = []);
        //return $this->authSuccess($client);
    }

}