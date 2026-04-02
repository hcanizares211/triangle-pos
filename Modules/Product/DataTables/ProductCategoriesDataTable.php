<?php

namespace Modules\Product\DataTables;

use Modules\Product\Entities\Category;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductCategoriesDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('product::categories.partials.actions', compact('data'));
            });
    }

    public function query(Category $model) {
        return $model->newQuery()->withCount('products');
    }

    public function html() {
        return $this->builder()
            ->setTableId('product_categories-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(4)
            ->parameters([
                'language' => [
                    'url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                ]
            ])
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel-fill"></i> ' . __('Excel')),
                Button::make('print')
                    ->text('<i class="bi bi-printer-fill"></i> ' . __('Print')),
                Button::make('reset')
                    ->text('<i class="bi bi-x-circle"></i> ' . __('Reset')),
                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat"></i> ' . __('Reload'))
            );
    }

    protected function getColumns() {
        return [
            Column::make('category_code')
                ->title(__('Category Code'))
                ->addClass('text-center'),

            Column::make('category_name')
                ->title(__('Category Name'))
                ->addClass('text-center'),

            Column::make('products_count')
                ->title(__('Products Count'))
                ->addClass('text-center'),

            Column::computed('action')
                ->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'ProductCategories_' . date('YmdHis');
    }
}
