@if($messages->count() == 0 && $order->supplier->email && $order->status == \Mss\Models\Order::STATUS_NEW)
    <a href="{{ route('order.message_create', ['order' => $order, 'sendorder' => 1]) }}" class="btn btn-lg btn-success">@lang('Send order to supplier via email')</a>
@endif

<order-messages :messages="{{ $messages }}" :order="{{ $order }}" :edit-enabled="{{ Auth()->user()->can('ordermessage.edit') ? 'true' : 'false' }}"></order-messages>

<assign-order-message-modal>{!! $dataTable->table() !!}</assign-order-message-modal>

<data-tables-filter>
    <data-tables-filter-select label="@lang('Supplier')" col-id="1" id="filterSupplier">
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

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush