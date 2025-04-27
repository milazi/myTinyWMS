@extends('layout.app')

@section('title', __('Orders'))

@section('breadcrumb')
    <li class="active">
        <strong>@lang('Overview')</strong>
    </li>
@endsection

@section('content')

    <div class="table-toolbar-right-content hidden">
        @can('order.create')
            <a href="{{ route('order.create') }}" class="btn btn-secondary">@lang('New Order')</a>
        @endcan
    </div>

    @can('ordermessage.view')
        @if($unassignedMessages)
        <div class="alert alert-warning mb-6">
            <strong>{{ $unassignedMessages }}</strong> @lang('unassigned new') {{ trans_choice('plural.message', $unassignedMessages) }} - <a class="alert-link" href="{{ route('order.messages_unassigned') }}">@lang('more') &raquo;</a>
        </div>
        @endif

        @if($assignedMessages->count())
        <div class="alert alert-success mb-6">
            <strong>@lang('New messages for the following orders'):</strong>
            <br>
            <br>
            <ul>
            @foreach($assignedMessages as $message)
                <li>
                    @if ($message->order)
                    <a href="{{ route('order.show', $message->order) }}" target="_blank">{{ $message->order->internal_order_number }} at {{ $message->order->supplier->name }}</a>
                    @else
                    {{ $message->id }}
                    @endif
                </li>
            @endforeach
            </ul>
        </div>
        @endif
    @endcan

    {!! $dataTable->table() !!}

    <data-tables-filter>
        <data-tables-filter-select label="@lang('Supplier')" col-id="1" id="filterSupplier">
            <option value=""></option>
            @foreach($supplier as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </data-tables-filter-select>

        <data-tables-filter-select label="@lang('Status')" col-id="3" id="filterStatus">
            <option value="open">@lang('open (new, ordered, partially delivered)')</option>
            <option value="{{ \Mss\Models\Order::STATUS_NEW }}">@lang('new')</option>
            <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">@lang('ordered')</option>
            <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">@lang('partially delivered')</option>
            <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">@lang('delivered')</option>
            <option value="{{ \Mss\Models\Order::STATUS_PAID }}">@lang('paid')</option>
            <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">@lang('cancelled')</option>
        </data-tables-filter-select>

        <data-tables-filter-select label="@lang('Invoice Status')" col-id="5" id="filterInvoiceStatus">
            <option value="empty">@lang('all')</option>
            <option value="none">@lang('open')</option>
            <option value="all">@lang('completely received')</option>
            <option value="partial">@lang('partially received')</option>
            <option value="check">@lang('in review')</option>
        </data-tables-filter-select>

        <data-tables-filter-select label="@lang('Order Confirmation Status')" col-id="4" id="filterABStatus">
            <option value="empty">@lang('all')</option>
            <option value="none">@lang('open')</option>
            <option value="all">@lang('completely received')</option>
            <option value="partial">@lang('partially received')</option>
        </data-tables-filter-select>
    </data-tables-filter>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush