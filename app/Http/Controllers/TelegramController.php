<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Telegram;
use Telegram\Bot\Objects\Update;

use App\Http\Controllers\Handlers\SimpleBotHandler;
use App\Http\Controllers\Handlers\InlineBotHandler;


class TelegramController extends Controller
{
    public function webhook(Update $update)
    {
        //Run CommandBus
        try
        {
            Telegram::commandsHandler(true);
        }
        catch(\Exception  $ex)
        {
        }

        //Check start command
        if (isset($update['message']['entities'][0]['type'])
            && $update['message']['entities'][0]['type'] == 'bot_command'
            && $update['message']['text'] == '/start') {
            return;
        }

        if (isset($update['inline_query'])) {
            InlineBotHandler::handle($update);
        }
        else if (isset($update['message']) || isset($update['callback_query'])) {
            SimpleBotHandler::handle($update);
        }

    }
}
