<?php

namespace Modules\Sale\DataTables;

use Modules\Sale\Entities\Sale;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalesDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('total_amount', function ($data) {
                return format_currency($data->total_amount);
            })
            ->addColumn('paid_amount', function ($data) {
                return format_currency($data->paid_amount);
            })
            ->addColumn('due_amount', function ($data) {
                return format_currency($data->due_amount);
            })
            ->addColumn('status', function ($data) {
                return view('sale::partials.status', compact('data'));
            })
            ->addColumn('payment_status', function ($data) {
                return view('sale::partials.payment-status', compact('data'));
            })
            ->addColumn('action', function ($data) {
                return view('sale::partials.actions', compact('data'));
            });
    }

    public function query(Sale $model) {
        return $model->newQuery();
    }

    public function html() {
        return $this->builder()
            ->setTableId('sales-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(8)
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
            Column::make('reference')
                ->title(__('Reference'))
                ->className('text-center align-middle'),

            Column::make('customer_name')
                ->title(__('Customer'))
                ->className('text-center align-middle'),

            Column::computed('status')
                ->title(__('Status'))
                ->className('text-center align-middle'),

            Column::computed('total_amount')
                ->title(__('Total Amount'))
                ->className('text-center align-middle'),

            Column::computed('paid_amount')
                ->title(__('Paid Amount'))
                ->className('text-center align-middle'),

            Column::computed('due_amount')
                ->title(__('Due Amount'))
                ->className('text-center align-middle'),

            Column::computed('payment_status')
                ->title(__('Payment Status'))
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Sales_' . date('YmdHis');
    }
}
