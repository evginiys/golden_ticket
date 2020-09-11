<?php

namespace app\modules\api\common\actions;

use app\models\Social;
use Exception;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\clients\VKontakte;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class VkontakteRegisterAction
 * @package app\modules\api\common\actions
 *
 * @SWG\Get(path="/user/vkontakte-register",
 *     tags={"Authentication"},
 *     summary="Performs authentication via VKontakte (vk.com) account.",
 *     @SWG\Response(
 *         response=302,
 *         description="Redirect"
 *     )
 * )
 */
class VkontakteRegisterAction extends AuthAction
{
    /**
     * @return mixed|Response
     */
    public function run()
    {
        try {
            $collection = Yii::$app->get($this->clientCollection);
            $clientId = 'vkontakte';
            if (!$collection->hasClient($clientId)) {
                throw new NotFoundHttpException("Unknown auth client '{$clientId}'");
            }
            $client = $collection->getClient($clientId);
            $client->validateAuthState = false;

            $request = Yii::$app->getRequest();
            $client->setReturnUrl(env('VK_REDIRECT'));
            if (($error = $request->get('error')) !== null) {
                if (
                    $error === 'access_denied' ||
                    $error === 'user_cancelled_login' ||
                    $error === 'user_cancelled_authorize'
                ) {
                    // user denied error
                    throw new Exception(Yii::t('app', $error));
                }
                // request error
                $errorMessage = $request->get('error_description', $request->get('error_message'));
                if ($errorMessage === null) {
                    $errorMessage = http_build_query($request->get());
                }
                throw new Exception('Auth error: ' . $errorMessage);
            }
            // Get the access_token
            if (($code = $request->get('code')) !== null) {
                $token = $client->fetchAccessToken($code);
                if (!empty($token)) {
                    return $this->success($client);
                }
                throw new Exception(Yii::t('app', 'Cannot get token'));
            }
            throw new Exception(Yii::t('app', 'Not found code in response'));
        } catch (Exception $e) {
            return $this->redirectWithParams(['error' => $e->getMessage()], env('REDIRECT_AUTHORIZED'));
        }
    }

    /**
     * @param VKontakte $client
     * @throws Exception
     *
     * @return resource redirect to url
     */
    public function success(VKontakte $client): mixed
    {
        $socialClient = $client->getId();
        $data = $client->getUserAttributes();
        $client_id = $data['id'];
        if ($socialUser = Social::findOne([
            'social_id' => $client_id,
            'social_client' => $socialClient
        ])) {
            $user = $socialUser->user;
            if ($user) {
                $token = $socialUser->signIn();
                return $this->redirectWithParams(['token' => $token], env('REDIRECT_AUTHORIZED'));
            } else {
                return $this->redirectWithParams(['social_id' => $socialUser->id], env('REDIRECT_AUTHORIZATION'));
            }
        } else {
            $socialUser = new Social([
                'social_id' => $client_id,
                'social_client' => $socialClient
            ]);
            if (!$socialUser->save()) {
                throw new Exception(Yii::t('app', "Incorrect social data"));
            }
            return $this->redirectWithParams(['social_id' => $socialUser->id], env('REDIRECT_AUTHORIZATION'));
        }
    }

    /**
     * @param array $data
     * @param string $redirectUrl
     *
     * @return resource redirect with params
     */
    public function redirectWithParams(array $data, string $redirectUrl): mixed
    {
        $params = http_build_query($data);
        return Yii::$app->response->redirect($redirectUrl . "?" . $params)->send();
    }

}