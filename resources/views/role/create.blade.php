@extends('role.form')

@section('title', __('New Role'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('role.index') }}">@lang('Roles Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('New Role')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($role, ['route' => ['role.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
@endsection