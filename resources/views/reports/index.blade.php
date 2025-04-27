@extends('layout.app')

@section('title', __('Reports'))

@section('content')
<div class="px-2 -ml-2" id="reports">
    <div class="flex -mx-2 flex-wrap">
        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">@lang('Inventory Evaluation')</div>
                <div class="card-content">
                    <small>@lang('Select month and inventory type to create report:')</small>

                    <form method="post" action="{{ route('reports.inventory_report') }}" id="inventory-report" class="mt-4">
                        {{ csrf_field() }}
                        <div class="flex flex-wrap">
                            <div class="w-full lg:w-1/2 pr-4">
                                {{ Form::bsSelect('inventorytype', null, \Mss\Models\Article::getInventoryTextArray(),   __('Inventory Type'), ['placeholder' => __('all')]) }}
                            </div>
                            <div class="w-full lg:w-1/2">
                                <label class="form-label">@lang('Month')</label>
                                <date-picker-input format="YYYY-MM" outputformat="YYYY-MM" type="inline" name="month" picker-class="w-auto"></date-picker-input>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-secondary">@lang('Create Report')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">@lang('GR Comparison')</div>
                <div class="card-content">
                    <small>@lang('Select month to create report')</small>

                    <form method="post" action="{{ route('reports.article_usage_report') }}" id="article-usage-report" class="mt-4">
                        {{ csrf_field() }}
                        <date-picker-input format="YYYY-MM" outputformat="YYYY-MM" type="inline" name="month" picker-class="w-auto"></date-picker-input>

                        <button type="submit" class="btn btn-secondary mt-4">@lang('Create Report')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">@lang('Goods Receipts with Invoice')</div>
                <div class="card-content">
                    <small>@lang('Select month to create report')</small>

                    <form method="post" action="{{ route('reports.invoices_with_delivery') }}" id="invoices-with-delivery" class="mt-4">
                        {{ csrf_field() }}
                        <div class="flex flex-wrap">
                            <div class="w-full lg:w-1/2 pr-4">
                                {{ Form::bsSelect('category', null, \Mss\Models\Category::orderBy('name')->pluck('name', 'id'),   __('Category'), ['placeholder' => 'alle']) }}
                            </div>
                            <div class="w-full lg:w-1/2">
                                <label class="form-label">@lang('Month')</label>
                                <date-picker-input format="YYYY-MM" outputformat="YYYY-MM" type="inline" name="month" picker-class="w-auto"></date-picker-input>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-secondary mt-4">@lang('Create Report')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">@lang('Print Stock List')</div>
                <div class="card-content">
                    <form method="post" action="{{ route('reports.print_category_list') }}" class="form-inline">
                        @csrf
                        {{ Form::bsSelect('category[]', null, \Mss\Models\Category::orderBy('name')->pluck('name', 'id'),   __('Select Categories'), ['multiple', 'size' => 12, 'class' => 'form-select h-auto']) }}
                        <div class="text-gray-600 text-xs mb-6">@lang('Multiple selection by holding down Ctrl (Mac: Cmd)')</div>
                        <button type="submit" class="btn btn-secondary pb-2"><i class="fa fa-download"></i> @lang('Download PDF') </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2">
            <div class="flex-column">
                <div class="card mb-4 h-40">
                    <div class="card-header">@lang('Monthly Inventory List')</div>
                    <div class="card-content">
                        <small class="block mb-4">@lang('only active consumables')</small>

                        <a class="btn btn-secondary pb-2" href="{{ route('reports.inventory_pdf') }}"><i class="fa fa-download"></i> @lang('Download PDF') </a>
                    </div>
                </div>

                <div class="card h-40">
                    <div class="card-header">@lang('Yearly Inventory List')</div>
                    <div class="card-content relative">
                        <small class="block mb-4">@lang('all active articles')</small>

                        <a class="btn btn-secondary pb-2" href="{{ route('reports.yearly_inventory_pdf') }}"><i class="fa fa-download"></i> @lang('Download PDF') </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2">
            <div class="flex-column">
                <div class="card mb-4 h-40">
                    <div class="card-header">@lang('Goods Receipts without Invoice')</div>
                    <div class="card-content">
                        <a class="btn btn-secondary pb-2" href="{{ route('reports.deliveries_without_invoice') }}"><i class="fa fa-arrow-right"></i> @lang('Show List') </a>
                    </div>
                </div>

                <div class="card mb-4 h-40">
                    <div class="card-header">@lang('Invoices without Goods Receipt')</div>
                    <div class="card-content">
                         <a class="btn btn-secondary pb-2" href="{{ route('reports.invoices_without_delivery') }}"><i class="fa fa-arrow-right"></i> @lang('Show List') </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2">
            <div class="flex">
                <div class="card mb-4 h-56">
                    <div class="card-header">@lang('Packaging Licensing Report')</div>
                    <div class="card-content">
                        <small>@lang('Select period to create report')</small>
                        <form method="post" action="{{ route('reports.article_weight_report') }}" id="article-weight-report" class="form-inline">
                            @csrf

                            <date-picker-input format="DD.MM.YYYY" outputformat="YYYY-MM-DD" name="daterange" picker-class="w-auto" :default="[]"></date-picker-input>
                            <button type="submit" class="btn btn-secondary mt-4">
                                <i class="fa fa-arrow-right"></i> @lang('Show List')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection