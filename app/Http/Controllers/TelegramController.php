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
            Telegram::commandsHandler(true);
        } 
        catch(\Exception  $ex)
        {
        }
    }
}
