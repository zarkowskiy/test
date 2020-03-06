<?php


namespace App\Http\Controllers\Handlers;

use Telegram;
use Telegram\Bot\Objects\Update;


class InlineBotHandler
{
    public static function handle(Update $update)
    {
        $idUser = $update['inline_query']['from']['id'];
    }
}