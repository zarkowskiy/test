<?php


namespace App\Http\Controllers\Handlers;

use App\Models\Cities;
use Telegram;
use Telegram\Bot\Objects\Update;


class SimpleBotHandler
{
    public static function handle(Update $update)
    {
        $idUser = $update['message']['from']['id'];

        if (isset($update['callback_query']['data'])) {
            $matches = explode('-', $update['callback_query']['data']);
            switch ($matches[0])
            {
                case 'page':
                    $cities = Cities::offset(env('PAGINATION_LIMIT') * $matches[1])->limit(env('PAGINATION_LIMIT'))->get();
                    $count = Cities::all()->count();
                    $pages = ceil($count / env('PAGINATION_LIMIT'));

                    $keyboard = self::getCitiesKeyboard($cities);

                    switch ($matches[1]) {
                        case $matches[1] == 1:
                            if ($count > env('PAGINATION_LIMIT')) {
                                $keyboard[] =
                                    [
                                        [
                                            'text' => 'next door',
                                            'callback_data' => "page-2"
                                        ]
                                    ];
                            }
                            break;
                        case $matches[1] > 1 && $matches[1] < $pages:
                            $keyboard[] =
                                [
                                    [
                                        'text' => 'prev door',
                                        'callback_data' => "page-".($matches[1]-1)
                                    ],
                                    [
                                        'text' => 'next door',
                                        'callback_data' => "page-".($matches[1]+1)
                                    ]
                                ];
                            break;
                        case $matches[1] == $pages:
                            $keyboard[] =
                                [
                                    [
                                        'text' => 'prev door',
                                        'callback_data' => "page-".$pages-1
                                    ]
                                ];
                            break;
                    }

                    Telegram::sendMessage(
                        [
                            'text' => "Выбери свой город",
                            'reply_markup' => $this->telegram->replyKeyboardMarkup(
                                [
                                    'inline_keyboard'=> $keyboard
                                ]
                            )
                        ]
                    );
                    break;
                case 'set':
                    switch ($matches[1])
                    {
                        case 'city':
                            break;
                    }
                    break;
            }
        }
    }

    public static function getCitiesKeyboard($cities)
    {
        $keyboard = [];

        for ($i = 0; $i<count($cities); $i+=2) {
            $row = [];
            $row[] =
                [
                    'text' => $cities[$i]->name,
                    'callback_data' => "set-city-$cities[$i]->idCity"
                ];
            if (isset($cities[$i+1])) {
                $row[] =
                    [
                        'text' => $cities[$i+1]->name,
                        'callback_data' => "set-city-$cities[$i]->idCity"
                    ];
            }
            $keyboard[] = $row;
        }

        return $keyboard;
    }
}