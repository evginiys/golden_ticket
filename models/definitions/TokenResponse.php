<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"error", "data"})
 *
 * @SWG\Property(property="error", type="integer", example=0)
 * @SWG\Property(
 *     property="data",
 *     type="object",
 *     @SWG\Property(property="token", type="string", example="TOKEN")
 * )
 */
class TokenResponse {}