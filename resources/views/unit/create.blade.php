@extends('unit.form')

@section('title', __('New Unit'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('unit.index') }}">@lang('Units Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('New Unit')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($unit, ['route' => ['unit.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
@endsection