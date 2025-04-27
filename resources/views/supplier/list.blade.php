@extends('layout.app')

@section('title', __('Suppliers'))

@section('breadcrumb')
    <li class="active">
        <strong>@lang('Overview')</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        @can('supplier.create')
        <a href="{{ route('supplier.create') }}" class="btn btn-secondary">@lang('New Supplier')</a>
        @endcan
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush