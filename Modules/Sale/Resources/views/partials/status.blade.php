@if ($data->status == 'Pending')
    <span class="badge badge-info">
        {{ __('Pending') }}
    </span>
@elseif ($data->status == 'Shipped')
    <span class="badge badge-primary">
        {{ __('Shipped') }}
    </span>
@else
    <span class="badge badge-success">
        {{ __($data->status) }}
    </span>
@endif
