<?php

namespace App\Commands;

use App\Http\Controllers\Handlers\SimpleBotHandler;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

use App\Models\TelegramUsers;
use App\Models\Cities;

use Carbon\Carbon;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $update = $this->getUpdate();
        $idUser = $update['message']['from']['id'];

        /*Update Telegram User*/
        $user = TelegramUsers::find($idUser);
        if (is_null($user))
        {
            $user = new TelegramUsers();
            $user->idTelegramUser = $idUser;
        }
        if (isset($update['message']['from']['username']))
        {
            $user->username = $update['message']['from']['username'];
        }
        if (isset($update['message']['from']['last_name']))
        {
            $user->lastname = $update['message']['from']['last_name'];
        }
        if (isset($update['message']['from']['first_name']))
        {
            $user->firstname = $update['message']['from']['first_name'];
        }

        $user->save();

        try
        {
            $cities = Cities::limit(env('PAGINATION_LIMIT'))->get();
            $count = Cities::all()->count();
            $keyboard = SimpleBotHandler::getCitiesKeyboard($cities);

            if ($count > env('PAGINATION_LIMIT')) {
                $keyboard[] =
                    [
                        [
                            'text' => 'next door',
                            'callback_data' => "page-2"
                        ]
                    ];
            }

            $this->replyWithSticker([
                'sticker' => url("images/1.webp")
            ]);
            $this->replyWithMessage(
                [
                    'text' => "Выбери свой город",
                    'reply_markup' => $this->telegram->replyKeyboardMarkup(
                        [
                            'inline_keyboard'=> $keyboard
                        ]
                    )
                ]
            );
        }
        catch(\Exception $ex)
        {
            $this->replyWithMessage(
                [
                    'text' => var_export($ex->getMessage(), true),
                    'reply_markup' => null
                ]
            );
        }
    }
}
