<a href="{{ route('unit.show', $id) }}" class="table-action">@lang('Details')</a>
<form action="{{ route('unit.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    @if ($model->articles->count() > 0)
        <button class="table-action btn-link" title="@lang('This unit still has articles assigned. It cannot be deleted!')" disabled="disabled">@lang('Delete')</button>
    @else
        <button class="table-action btn-link" onclick="return confirm('@lang('Are you sure?')')">@lang('Delete')</button>
    @endif
</form>