@extends('article.form')

@section('title', __('New Article'))

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('New Article')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($article, ['route' => ['article.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary', 'id' => 'submit']) !!}
@endsection

@section('secondCol')
    <div class="w-1/3 ml-4">
        <div class="card">
            <div class="card-header">@lang('Supplier')</div>
            <div class="card-content">
                {{ Form::bsSelect('supplier_id', optional($article->currentSupplierArticle)->supplier_id, \Mss\Models\Supplier::orderedByName()->pluck('name', 'id'),  __('Supplier'), ['placeholder' => '', 'required' => 'required']) }}
                {{ Form::bsText('supplier_order_number', optional($article->currentSupplierArticle)->order_number, [], __('Order Number')) }}
                {{ Form::bsText('supplier_price', optional($article->currentSupplierArticle)->price ? optional($article->currentSupplierArticle)->price / 100 : null, [], __('Net Price')) }}
                {{ Form::bsText('supplier_delivery_time', optional($article->currentSupplierArticle)->delivery_time, [], __('Delivery Time (Working Days)')) }}
                {{ Form::bsNumber('supplier_order_quantity', optional($article->currentSupplierArticle)->order_quantity, [], __('Order Quantity')) }}
            </div>
        </div>
    </div>
@endsection