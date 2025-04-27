@extends('layout.app')

@section('title', __('New Messages'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Orders')</a>
    </li>
    <li class="active">
        <strong>@lang('New Messages')</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="card w-full">
            <div class="card-content">
                <order-messages :messages="{{ $unassignedMessages }}" :edit-enabled="{{ Auth()->user()->can('ordermessage.edit') ? 'true' : 'false' }}"></order-messages>
            </div>
        </div>
    </div>


    <assign-order-message-modal>{!! $dataTable->table() !!}</assign-order-message-modal>

    <data-tables-filter>
        <data-tables-filter-select label="Supplier" col-id="1" id="filterSupplier">
            <option value=""></option>
            @foreach(\Mss\Models\Supplier::orderedByName()->get() as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </data-tables-filter-select>

        <data-tables-filter-select label="Status" col-id="3">
            <option value="open">@lang('open (new, ordered, partially delivered)')</option>
            <option value="{{ \Mss\Models\Order::STATUS_NEW }}">@lang('new')</option>
            <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">@lang('ordered')</option>
            <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">@lang('partially delivered')</option>
            <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">@lang('delivered')</option>
            <option value="{{ \Mss\Models\Order::STATUS_PAID }}">@lang('paid')</option>
            <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">@lang('cancelled')</option>
        </data-tables-filter-select>
    </data-tables-filter>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush