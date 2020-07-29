<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"id", "ticket_pack_id", "cost", "is_active"})
 *
 * @SWG\Property(property="id", type="integer", example=1)
 * @SWG\Property(property="ticket_pack_id", type="integer", example=1)
 * @SWG\Property(property="cost", type="number", example=5.0)
 * @SWG\Property(property="is_active", type="integer", example=1, description="`1` for Active, `0` for Inactive")
 */
class Ticket {}