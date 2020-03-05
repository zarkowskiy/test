<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Encore\Admin\Widgets\Form;
use App\Settings;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;

class WebhookController extends Controller
{
    public function index(Content $content)
    {
        
        $webhook_url = Settings::where("key", "=", "webhook_url")->first();
        $form = new Form();
        $form->action(route("admin.webhook.setwebhook"));
        $form->method("POST");
        $form->token();
        $form->url('oldUrl', "Текущий вебхук")->readonly()->default(!is_null($webhook_url)?$webhook_url->value:"");
        $form->url('url', "Новый вебхук");
        $form->disableReset();
        $form->disableSubmit();
        $form->html('<button type="submit" class="btn btn-success">Сохранить</button>');
        return $content
            ->header('Настройки вебхука')
            ->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    if(Session::has('status'))
                    {
                        $box = new Box('Status', Session::get('status'));
                        $box->removable();
                        $column->append($box);
                    }
                });
            })
            ->row(function (Row $row) use ($form) {
                $row->column(12, function (Column $column) use ($form) {
                    $column->append($form->render());
                });
            });
    }
    public function setWebhook(Request $request){
        $result = $this->sendTelegramData(
            'setwebhook',
            [
                'query' => 
                [
                    'url'=>$request->url . '/' . \Telegram::getAccessToken(),
                ],
            ]
        );
        Settings::where('key','=',"webhook_url")->delete();
        $setting = new Settings;
        $setting->key = "webhook_url";
        $setting->value = $request->url;
        $setting->save();
        return redirect()
            ->route('admin.webhook.index')
            ->with('status', $result);
    }

    protected function getWebhookInfo(Request $request){
        $result = $this->sendTelegramData(
            'getWebhookInfo'
        );
        return redirect()
            ->route('admin.webhook.index')
            ->with('status', $result);
    }

    protected function sendTelegramData($route = '', $params = [], $method = 'POST')
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.telegram.org/bot' . \Telegram::getAccessToken() . '/'
        ]);
        $result  = $client->request($method, $route, $params);
        return (string) $result->getBody();
    }
}
