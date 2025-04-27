@extends('supplier.form')

@section('title', __('Edit Supplier'))

@section('breadcrumb')
    <li>
        <a href="{{ route('supplier.index') }}">@lang('Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('Edit Supplier')</strong>
    </li>
@endsection

@section('form_start')
    @can('supplier.edit')
    {!! Form::model($supplier, ['route' => ['supplier.update', $supplier], 'method' => 'PUT']) !!}
    @endcan
@endsection

@section('submit')
    @can('supplier.edit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
    @endcan
@endsection

@section('secondCol')
    <div class="w-1/3 ml-4">
        <collapse title="@lang('Logbook')">
            @include('components.audit_list', $audits)
        </collapse>
    </div>
@endsection
