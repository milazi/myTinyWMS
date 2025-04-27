@extends('order.form')

@section('title', __('New Order'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('New Order')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($order, ['route' => ['order.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary', 'id' => 'save-order']) !!}
    <a href="{{ route('order.cancel', $order) }}" class="btn btn-danger pull-right">@lang('Cancel')</a>
@endsection