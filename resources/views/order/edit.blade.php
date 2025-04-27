@extends('order.form')

@section('title', __('Edit Order'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Overview')</a>
    </li>
    <li>
        <a href="{{ route('order.show', $order) }}">@lang('Order') #{{ $order->internal_order_number }}</a>
    </li>
    <li class="active">
        <strong>@lang('Edit Order')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($order, ['route' => ['order.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
@endsection