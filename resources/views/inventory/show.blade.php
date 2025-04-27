@extends('unit.form')

@section('title', __('Edit Inventory - started on ').$inventory->created_at->format('m/d/Y H:i'))

@section('breadcrumb')
    <li>
        <a href="{{ route('inventory.index') }}">@lang('Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('Edit Inventory')</strong>
    </li>
@endsection

@section('content')
    <inventory-articles :items="{{ json_encode($items) }}" :inventory="{{ json_encode($inventory) }}" :inventory-is-finished="{{ json_encode($inventory->isFinished()) }}" :edit-enabled="{{ Auth()->user()->can('inventory.edit') ? 'true' : 'false' }}"></inventory-articles>
@endsection