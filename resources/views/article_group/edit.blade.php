@extends('article_group.form')

@section('title', __('Edit Article Group'))

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Articles')</a>
    </li>
    <li>
        <a href="{{ route('article-group.index') }}">@lang('Manage Article Groups')</a>
    </li>
    <li class="active">
        <strong>@lang('Edit Article Group')</strong>
    </li>
@endsection

@section('form_start')
    @can('article-group.edit')
    {!! Form::model($articleGroup, ['route' => ['article-group.update', $articleGroup], 'method' => 'PUT']) !!}
    @endcan
@endsection

@section('submit')
    @can('supplier-group.edit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary', 'id' => 'submit']) !!}
    @endcan
@endsection

@section('secondCol')
    <collapse title="@lang('Logbook')">
        @include('components.audit_list', $audits)
    </collapse>
@endsection