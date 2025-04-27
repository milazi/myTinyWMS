@extends('category.form')

@section('title', __('Edit Category'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('category.index') }}">@lang('Categories Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('Edit Category')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($category, ['route' => ['category.update', $category], 'method' => 'PUT']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
@endsection

@section('secondCol')
    <div class="w-1/3 ml-4">
        <collapse title="@lang('Logbook')">
            @include('components.audit_list', $audits)
        </collapse>
    </div>
@endsection