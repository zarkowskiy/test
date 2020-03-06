<?php

namespace App\Admin\Controllers;

use App\Models\Cities;
use App\Models\Places;
use App\Models\Schedules;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CitiesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Города';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Cities());

        $grid->column('name', __('Название'))->filter("like")->sortable();
        $grid->column('places',__('Места'))->display(function ($places){
            return "<a href='".
                route(
                    "admin.cities.places.index",
                    [
                        "city" => $this->getKey()
                    ]
                )."' class='btn btn-sm btn-default'>Список (".count($places).")</a>";
        });
        $grid->actions(function (Grid\Displayers\Actions $actions){
            $actions->disableView();
            $actions->disableEdit();
        });
        $grid->disableCreateButton();
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->disableFilter();
        $grid->quickCreate(function (Grid\Tools\QuickCreate $create){
            $create->text('name',"Название");
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @return void
     */
    protected function detail()
    {
        abort(404);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Cities());

        $form->text('name', __('Название'));

        return $form;
    }

    /**
     * @param Content $content
     * @param Cities $city
     * @return Content
     */
    public function places_index(Content $content, Cities $city){
        $grid = new Grid(new Places());
        $grid->model()->where('idCity',$city->idCity);
        $grid->column('name','Название')
            ->filter('like')
            ->sortable();
        $grid->column('address','Адрес')
            ->filter('like')
            ->sortable();
        $grid->column('schedule','Расписание')->display(function () use ($city){
            return "<a href='".
                route(
                    "admin.cities.places.schedule.edit",
                    [
                        "city" => $city->idCity,
                        "place"=>$this->getKey()
                    ]
                )."' class='btn btn-sm btn-default'>Редактировать</a>";
        });
        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableColumnSelector();
        $grid->disableRowSelector();
        $grid->disableBatchActions();
        $grid->actions(function (Grid\Displayers\Actions $actions){
            $actions->disableView();
        });

        return $content
            ->header("Места города $city->name")
            ->row(function (Row $row) use($grid) {
                $row->column(12, function (Column $column) use($grid) {
                    $column->append($grid->render());
                });
            });
    }

    /**
     * @param Content $content
     * @param Cities $city
     * @return Content
     */
    public function places_create(Content $content, Cities $city){
        $form = $this->places_form($city);
        return $content
            ->header("Места города $city->name")
            ->row(function (Row $row) use($form) {
                $row->column(12, function (Column $column) use($form) {
                    $column->append($form->render());
                });
            });
    }

    /**
     * @param Content $content
     * @param Cities $city
     * @param Places $place
     * @return Content
     */
    public function places_edit(Content $content, Cities $city, Places $place){
        $form = $this->places_form($city);
        $form->edit($place->idPlace);
        return $content
            ->header("Места города $city->name")
            ->row(function (Row $row) use($form) {
                $row->column(12, function (Column $column) use($form) {
                    $column->append($form->render());
                });
            });
    }

    /**
     * @param Cities $city
     * @return Form
     */
    protected function places_form(Cities $city){
        $form = new Form(new Places);

        $form->hidden("idPlace");
        $form->hidden("idCity")->default($city->idCity);
        $form->image("image", "Логотип");
        $form->text('name',"Название")->required();
        $form->textarea('address',"Адрес");
        $form->textarea('description',"Описание");

        $form->tools(function (Form\Tools $tools){
            $tools->disableView();
            $tools->disableList();
            $tools->disableDelete();
        });

        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->disableReset();
        return $form;
    }

    /**
     * @param Request $request
     * @param Cities $city
     * @param Places|null $place
     * @return RedirectResponse
     */
    public function places_save(Request $request, Cities $city, Places $place = null){
        if (is_null($place)){
            $place = new Places();
        }
        $place->fill($request->all());
        if ($request->has('image')){
            if ($place->image != $request->get('image')){
                $file = $request->file('image');
                $storagePath = Storage::disk('admin')->put("images",$file);
                $place->image = "images/".basename($storagePath);
            }
        }
        $place->save();
        admin_toastr(__('admin.save_succeeded'));
        return redirect()->route('admin.cities.places.index',['city'=>$request->get('idCity')]);
    }

    public function schedule_edit(Content $content,Cities $city, Places $place){
        $form = $this->schedule_form($place);
        if (!is_null($place->schedule)){
            $form->edit($place->schedule->idSchedule);
        }
        return $content
            ->header("Расписание $place->name")
            ->row(function (Row $row) use($form) {
                $row->column(12, function (Column $column) use($form) {
                    $column->append($form->render());
                });
            });
    }
    protected function schedule_form(Places $place){
        $form = new Form(new Schedules);
        $form->setAction(
            route(
                'admin.cities.places.schedule.save',
                [
                    'city' => $place->idCity,
                    'place' => $place->idPlace
                ]
            )
        );
        $form->hidden("idPlace")->default($place->idPlace);
        $form->embeds('scheduledJSON',"Расписание", function (Form\EmbeddedForm $form){
            $form->html("<h4>Понедельник</h4>");
            $form->time("mondayOpen","Открывается")->format('HH:mm')->required();
            $form->time("mondayClose","Закрывается")->format('HH:mm')->required();
            $form->html("<h4>Вторник</h4>");
            $form->time("tuesdayOpen","Открывается")->format('HH:mm')->required();
            $form->time("tuesdayClose","Закрывается")->format('HH:mm')->required();
            $form->html("<h4>Среда</h4>");
            $form->time("wednesdayOpen","Открывается")->format('HH:mm')->required();
            $form->time("wednesdayClose","Закрывается")->format('HH:mm')->required();
            $form->html("<h4>Четверг</h4>");
            $form->time("thursdayOpen","Открывается")->format('HH:mm')->required();
            $form->time("thursdayClose","Закрывается")->format('HH:mm')->required();
            $form->html("<h4>Пятница</h4>");
            $form->time("fridayOpen","Открывается")->format('HH:mm')->required();
            $form->time("fridayClose","Закрывается")->format('HH:mm')->required();
            $form->html("<h4>Субота</h4>");
            $form->time("saturdayOpen","Открывается")->format('HH:mm')->required();
            $form->time("saturdayClose","Закрывается")->format('HH:mm')->required();
            $form->html("<h4>Воскресенье</h4>");
            $form->time("sundayOpen","Открывается")->format('HH:mm')->required();
            $form->time("sundayClose","Закрывается")->format('HH:mm')->required();
        });

        $form->tools(function (Form\Tools $tools){
            $tools->disableView();
            $tools->disableList();
            $tools->disableDelete();
        });

        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->disableReset();
        return $form;
    }
    public function schedule_save(Request $request, Cities $city, Places $place){
        Schedules::updateOrCreate(
            [
                'idPlace' => $place->idPlace
            ],
            $request->all()
        );
        admin_toastr(__('admin.save_succeeded'));
        return redirect()
            ->route(
                'admin.cities.places.index',
                [
                    'city' => $city->idCity
                ]
            );
    }

}
