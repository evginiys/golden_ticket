<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"name", "message", "code", "status", "type"})
 *
 * @SWG\Property(property="name", type="string", example="Unauthorized")
 * @SWG\Property(property="message", type="string", example="Your request was made with invalid credentials.")
 * @SWG\Property(property="code", type="integer", example=0)
 * @SWG\Property(property="status", type="integer", example=401)
 * @SWG\Property(property="type", type="string", example="yii\web\UnauthorizedHttpException")
 */
class UnauthorizedResponse {}