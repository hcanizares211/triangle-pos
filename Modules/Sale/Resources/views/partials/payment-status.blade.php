@if ($data->payment_status == 'Partial')
    <span class="badge badge-warning">
        {{ __('Partial') }}
    </span>
@elseif ($data->payment_status == 'Paid')
    <span class="badge badge-success">
        {{ __('Paid') }}
    </span>
@else
    <span class="badge badge-danger">
        {{ __($data->payment_status) }}
    </span>
@endif
