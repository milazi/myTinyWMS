<a href="{{ route('category.show', $id) }}" class="table-action">@lang('Details')</a>
<form action="{{ route('category.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    @if ($model->articles->count() > 0)
        <button class="table-action btn-link" title="@lang('This category still has articles assigned to it. It cannot be deleted!')" disabled="disabled">@lang('Delete')</button>
    @else
        <button class="table-action btn-link" onclick="return confirm('@lang('Really delete?')')">@lang('Delete')</button>
    @endif
</form>