@if ($data->status == 'Pending')
    <span class="badge badge-info">
        {{ __($data->status) }}
    </span>
@else
    <span class="badge badge-success">
        {{ __($data->status) }}
    </span>
@endif
