@extends('user.form')

@section('title', __('Edit User'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('user.index') }}">@lang('Users Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('Edit User')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($user, ['route' => ['user.update', $user], 'method' => 'PUT']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
@endsection