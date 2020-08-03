<?php

namespace app\models\definitions;

/**
 * @SWG\Definition(required={"id", "type", "date_start", "cost", "date_end", "status", "archive_url"})
 *
 * @SWG\Property(property="id", type="integer", example=1, description="ID of a game")
 * @SWG\Property(
 *     property="type",
 *     type="integer",
 *     example=0,
 *     description="Type of a game: `0` for Regular and `1` for Jackpot"
 * )
 * @SWG\Property(
 *     property="date_start",
 *     type="string",
 *     example="2020-07-01 10:15:00",
 *     description="Date and time of game start in the following format: `YYYY-MM-DD HH:MM:SS`"
 * )
 * @SWG\Property(property="cost", type="number", example=100.0, description="Cost of a game")
 * @SWG\Property(
 *     property="date_end",
 *     type="string",
 *     example="2020-07-01 10:21:03",
 *     description="Date and time of game end in the following format: `YYYY-MM-DD HH:MM:SS`"
 * )
 * @SWG\Property(
 *     property="status",
 *     type="integer",
 *     example=2,
 *     description="Current status of a game: `0` for Scheduled, `1` for In Process, `2` for Ended"
 * )
 * @SWG\Property(
 *     property="archive_url",
 *     type="string",
 *     example="combination_1593598500.zip",
 *     description="A link to password-protected ZIP-archive containing a text file with game combinations"
 * )
 */
class Game {}