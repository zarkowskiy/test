<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Telegram;
use Telegram\Bot\Objects\Update;


class TelegramController extends Controller
{
    public function webhook(Update $update)
    {
        try
        {
            Telegram::sendMessage([
                'chat_id' => '403914206',
                'text' => var_export($update, true)
            ]);
            Telegram::commandsHandler(true);
        } 
        catch(\Exception  $ex)
        {
        }
    }
}
