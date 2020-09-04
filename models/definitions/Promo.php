<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"id", "name", "description", "cost", "imageUrl", "expiration_at", "created_at"})
 *
 * @SWG\Property(property="id", type="integer", example=1)
 * @SWG\Property(property="name", type="string", example="Summer Discount For Service")
 * @SWG\Property(property="description", type="string", example="50% subscription discount for 2 months after registration")
 * @SWG\Property(property="cost", type="float", example=15)
 * @SWG\Property(property="imageUrl", type="string", description="URL to featured image")
 * @SWG\Property(property="expiration_at", type="string", description="Date of expiration of promo", example="2020-09-01 00:00:00")
 * @SWG\Property(property="created_at", type="string", description="Date of creation of promo", example="2020-06-01 08:01:00")
 */
class Promo {}