<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"error", "data"})
 *
 * @SWG\Property(property="error", type="integer", example=1, description="Indicates that an error occured")
 * @SWG\Property(property="data", type="object", @SWG\Schema(), description="Value of *any* type")
 */
class ErrorResponse {}