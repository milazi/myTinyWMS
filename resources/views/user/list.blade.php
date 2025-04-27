@extends('layout.app')

@section('title', __('Users Overview'))


@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li class="active">
        <strong>@lang('Users Overview')</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('role.index') }}" class="btn btn-secondary mr-4">@lang('Manage Roles')</a>
        <a href="{{ route('user.create') }}" class="btn btn-secondary">@lang('New User')</a>
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush