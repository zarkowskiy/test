<?php

namespace App\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

use App\Groups;
use App\TelegramUsers;

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
        
        try
        {
            $this->replyWithMessage(
                [
                    'text' => "Hello World!",
                    'reply_markup' => null
                ]
            );
        }
        catch(\Exception $ex)
        {
        }
    }
}
