@extends('layout.app')

@section('title', __('Categories'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li class="active">
        <strong>@lang('Categories Overview')</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('category.create') }}" class="btn btn-secondary">@lang('New Category')</a>
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush