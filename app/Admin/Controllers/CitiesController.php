<?php

namespace App\Admin\Controllers;

use App\Models\Cities;
use App\Models\Places;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;

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
            return count($places);
            //admin.cities.places.index
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

    public function places_grid(Content $content, Cities $city){
        $grid = new Grid(new Places());
        $grid->model()->where('idCity',$city->icCity);
        return $content
            ->header("Места города $city->name")
            ->row(function (Row $row) use($grid) {
                $row->column(12, function (Column $column) use($grid) {
                    $column->append($grid->render());
                });
            });

    }
}
