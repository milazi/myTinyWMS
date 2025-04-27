@extends('article_group.form')

@section('title', __('New Article Group'))

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Articles')</a>
    </li>
    <li>
        <a href="{{ route('article-group.index') }}">@lang('Manage Article Groups')</a>
    </li>
    <li class="active">
        <strong>@lang('New Article Group')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($articleGroup, ['route' => ['article-group.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary', 'id' => 'submit']) !!}
@endsection