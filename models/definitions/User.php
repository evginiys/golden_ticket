<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"username", "email", "phone"})
 *
 * @SWG\Property(property="username", type="string", example="John Doe")
 * @SWG\Property(property="phone", type="string", example="88001234567")
 * @SWG\Property(property="email", type="string", example="john.doe@example.com")
 */
class User {}