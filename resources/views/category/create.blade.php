@extends('category.form')

@section('title', __('New Category'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('category.index') }}">@lang('Categories Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('New Category')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($category, ['route' => ['category.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
@endsection