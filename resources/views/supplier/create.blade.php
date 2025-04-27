@extends('supplier.form')

@section('title', __('New Supplier'))

@section('breadcrumb')
    <li>
        <a href="{{ route('supplier.index') }}">@lang('Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('New Supplier')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($supplier, ['route' => ['supplier.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
@endsection