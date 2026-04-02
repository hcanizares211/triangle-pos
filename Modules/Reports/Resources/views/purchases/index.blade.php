@extends('layouts.app')

@section('title', __('Purchases Report'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('Purchases Report') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:reports.purchases-report :suppliers="\Modules\People\Entities\Supplier::all()"/>
    </div>
@endsection
