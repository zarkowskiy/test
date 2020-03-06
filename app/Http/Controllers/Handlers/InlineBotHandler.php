<?php


namespace App\Http\Controllers\Handlers;

use App\Models\Places;
use App\Models\TelegramUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Telegram;
use Telegram\Bot\Objects\Update;


class InlineBotHandler
{
    public static function handle(Update $update)
    {
        try{
            $idUser = $update['inline_query']['from']['id'];
            $user = TelegramUsers::find($idUser);
            $query = $update['inline_query']['query'];
            if (!is_null($user)&&!is_null($user->idCity)){
                $results = Places::where('idCity',$user->idCity);
                if (!empty($query)){
                    $results->where('name','like',"%$query%");
                }
                $collection = $results->with('schedule')->get();
                $time = Carbon::now();
                $collection->filter(function ($value, $key) use ($time){
                    if (is_null($value->schedule)){
                        return false;
                    }
                    return self::isOpened($value->schedule->scheduledJSON, $time);
                });
                $inlineQueryResults = [];
                foreach ($collection->toArray() as $place){
                    $inlineQueryResults[] = [
                        'type' => 'article',
                        'id' => 'place-'.$place['idPlace'],
                        'title' => $place['name'],
                        'input_message_content' => [
                            'message_text' => "<b>$place[name]</b>".PHP_EOL."<i>$place[description]</i>",
                            'parse_mode' => "html"
                        ],
                        'description' => substr($place['description'],0,100),
                        'thumb_url' => is_null($place['image']) ? "" : Storage::disk('admin')->url($place['image']),
                        'thumb_width' => 200,
                        'thumb_height' => 200,
                        'reply_markup' => [
                            'inline_keyboard' => [
                                [
                                    [
                                        'text' => "\xF0\x9F\x94\x94	Бронировать",
                                        'callback_data' => 'bron',
                                    ],
                                    [
                                        'text' => "Меню \xF0\x9F\x8D\xB4 Контакты",
                                        'callback_data' => 'menu',
                                    ],
                                ]
                            ]
                        ]
                    ];
                }

                Telegram::answerInlineQuery([
                    'inline_query_id' => $update['inline_query']['id'],
                    'results' => $inlineQueryResults,
                    'cache_time' => 0,
                    'is_personal' => true
                ]);
            }
        }
        catch (\Exception $exception){
            Telegram::sendMessage([
                'chat_id' => "641597655",
                'text' => var_export($exception->getMessage().$exception->getLine(),true)
            ]);
        }
    }

    /**
     * @param array $schedule
     * @param Carbon $time
     * @return bool
     */
    protected static function isOpened(array $schedule, Carbon $time){
        if (!empty($schedule)) {
            $startTime = null;
            $endTime = null;
            $day = $time->format('L');
            if (!empty($schedule)&&isset($schedule["{$day}Open"])&&isset($schedule["{$day}Close"])){
                $startTime = clone $time;
                $startTime->setTimeFromTimeString($schedule["{$day}Open"]);
                $endTime = clone $time;
                $endTime->setTimeFromTimeString($schedule["{$day}Close"]);
                if ($startTime->lte($time) && $endTime->gt($time)){
                    return true;
                }
            }
        }
        return false;
    }
}