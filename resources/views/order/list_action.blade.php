<a href="{{ route('order.show', $id) }}" target="_blank" class="table-action">@lang('Details')</a>

@can('order.delete')
<form action="{{ route('order.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    <button class="table-action btn-link" onclick="return confirm('Really delete?')">@lang('Delete')</button>
</form>
@endcan