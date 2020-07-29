<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"id", "name", "is_active"})
 *
 * @SWG\Property(property="id", type="integer", example=1)
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="is_active", type="integer", example=1, description="`1` for Active, `0` for Inactive")
 */
class TicketPack {}