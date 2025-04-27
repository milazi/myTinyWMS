@extends('layout.app')

@section('title', __('Article Groups'))

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Articles')</a>
    </li>
    <li class="active">
        <strong>@lang('Manage Article Groups')</strong>
    </li>
@endsection

@section('content')

    <div class="table-toolbar-right-content hidden">
        @can('article-group.create')
            <a href="{{ route('article-group.create') }}" class="btn btn-secondary">@lang('New Article Group')</a>
        @endcan
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush