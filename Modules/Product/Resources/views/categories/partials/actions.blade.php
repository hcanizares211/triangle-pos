<a href="{{ route('product-categories.edit', $data->id) }}" class="btn btn-info btn-sm" title="{{ __('Edit') }}">
    <i class="bi bi-pencil"></i>
</a>
<button id="delete" class="btn btn-danger btn-sm" title="{{ __('Delete') }}" onclick="
    event.preventDefault();
    if (confirm('{{ __('Are you sure? It will delete the data permanently!') }}')) {
        document.getElementById('destroy{{ $data->id }}').submit();
    }
    ">
    <i class="bi bi-trash"></i>
    <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('product-categories.destroy', $data->id) }}" method="POST">
        @csrf
        @method('delete')
    </form>
</button>
