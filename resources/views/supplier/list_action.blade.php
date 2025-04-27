<a href="{{ route('supplier.show', $id) }}" class="table-action">@lang('Details')</a>
<form action="{{ route('supplier.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    @if ($model->articles->count() > 0)
        <button class="btn-link table-action mt-2" data-toggle="tooltip" data-placement="left" title="This supplier still has articles assigned. It cannot be deleted!" disabled="disabled">@lang('Delete')</button>
    @else
        <button class="btn-link table-action mt-2" onclick="return confirm('@lang('Are you sure?')')">@lang('Delete')</button>
    @endif
</form>