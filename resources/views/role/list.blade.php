@extends('layout.app')

@section('title', __('Roles Overview'))


@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li class="active">
        <strong>@lang('Roles Overview')</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('role.create') }}" class="btn btn-secondary">@lang('New Role')</a>
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush