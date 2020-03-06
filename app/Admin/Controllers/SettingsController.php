<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Encore\Admin\Widgets\Form;
use App\Models\Settings;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\Str;
use Telegram\Bot\Api;

class SettingsController extends Controller
{
    /**
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $tab = new Tab();

        //Webhook settings
        $webhookForm = new Form();
        $webhookForm->action(route("admin.settings.setwebhook"));
        $webhookForm->method("POST");
        $webhookForm->token();

        $webhookForm->url('webhook_url', "Вебхук")->default(
            Settings::find("webhook_url")['value'] ?? ""
        );

        $webhookForm->disableReset();
        $webhookForm->disableSubmit();
        $webhookForm->html('<button type="submit" class="btn btn-success">Сохранить</button>');
        $tab->add('Настройки веб-хука', $webhookForm->render());

        $welcomeForm = new Form();
        $welcomeForm->action(route("admin.settings.setwelcome"));
        $welcomeForm->method("POST");
        $welcomeForm->token();

        $oldUrl = Settings::find("logo_url");

        if (!is_null($oldUrl)){
            $welcomeForm->html("<img src='".Storage::disk('admin')->url("images/$oldUrl[value]")."' class='img-thumbnail' style='max-width: 300px'/>");
        }
        $welcomeForm->file('logo_url', "Логотип");
        $welcomeForm->textarea('welcome_text', "Приветствие")->default(
            Settings::find("welcome_text")['value'] ?? ""
        );
        $welcomeForm->disableReset();
        $welcomeForm->disableSubmit();
        $welcomeForm->html('<button type="submit" class="btn btn-success">Сохранить</button>');
        $tab->add('Настройки приветствия', $welcomeForm->render());
        return $content
            ->header('Настройки')
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
            ->row(function (Row $row) use ($tab) {
                $row->column(12, function (Column $column) use ($tab) {
                    $column->append($tab->render());
                });
            });
    }
    public function setWebhook(Request $request)
    {
        if (!is_null($request->webhook_url))
        {
            $result = false;
            try
            {
                $apiClient = new Api();
                $apiClient->setAccessToken(\Telegram::getAccessToken());
                $response = $apiClient->setWebhook(
                    [
                        "url" => $request->webhook_url . '/' . \Telegram::getAccessToken()
                    ]
                );

                if ($response->isError())
                {
                    $error = new MessageBag([
                        'title'   => __('Ошибка при сохранении'),
                        'message' => __('Проверьте пожалуйста правильность введённых данных'),
                    ]);

                    return back()->with(compact('error'));
                }
                else {
                    $result = true;
                }
            }
            catch (\Exception $ex)
            {
            }
            if (!is_null($request->webhook_url))
            {
                Settings::updateOrCreate(
                    ['key' => "webhook_url"],
                    ['value' => $request->webhook_url]
                );
            }
        }
        return redirect()
            ->route('admin.settings.index')
            ->with('status', $result?"Веб-хук установлен успешно!":"Произошла ошибка при установке веб-хука");
    }
    public function setWelcome(Request $request)
    {
        if (!is_null($request->welcome_text))
        {

            Settings::updateOrCreate(
                ['key' => "welcome_text"],
                ['value' => $request->welcome_text]
            );
        }
        if ($request->files->has("logo_url")) {
            $file = $request->file('logo_url');
            $storagePath = Storage::disk('admin')->put("images",$file);
            Settings::updateOrCreate(
                ['key' => "logo_url"],
                ['value' => basename($storagePath)]
            );
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('status',"Настройки успешно изменены!");
    }
}
