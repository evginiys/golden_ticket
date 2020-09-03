<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"id", "name", "description", "cost", "imageUrl", "expiration_at", "created_at"})
 *
 * @SWG\Property(property="id", type="integer", example=1)
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="description", type="string")
 * @SWG\Property(property="cost", type="float", example=15)
 * @SWG\Property(property="imageUrl", type="string", description="URL to featured image")
 * @SWG\Property(property="expiration_at", type="string", description="Date of expiration of promo")
 * @SWG\Property(property="created_at", type="string", description="Date of creation of promo")
 */
class Promo {}