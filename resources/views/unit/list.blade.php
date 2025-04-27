@extends('layout.app')

@section('title', __('Units Overview'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li class="active">
        <strong>@lang('Units Overview')</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('unit.create') }}" class="btn btn-secondary">@lang('New Unit')</a>
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush