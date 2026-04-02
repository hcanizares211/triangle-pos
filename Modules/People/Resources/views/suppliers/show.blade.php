@extends('layouts.app')

@section('title', 'Supplier Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">{{ __('Suppliers') }}</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('Supplier Name') }}</th>
                                    <td>{{ $supplier->supplier_name }}</td>
                                </tr>
                                <tr>
                                    <th>Supplier Email</th>
                                    <td>{{ $supplier->supplier_email }}</td>
                                </tr>
                                <tr>
                                    <th>Supplier Phone</th>
                                    <td>{{ $supplier->supplier_phone }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('City') }}</th>
                                    <td>{{ $supplier->city }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Country') }}</th>
                                    <td>{{ $supplier->country }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address') }}</th>
                                    <td>{{ $supplier->address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

