<a href="{{ route('inventory.show', $id) }}" class="table-action">@lang('continue')</a>
@can('inventory.edit')
<a href="{{ route('inventory.finish', $id) }}" class="table-action" onclick="return confirm('@lang('Are you sure?')')">@lang('complete')</a>
@endcan