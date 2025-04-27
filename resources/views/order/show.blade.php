@extends('layout.app')

@section('title', __('Order at ').optional($order->supplier)->name)

@section('title_extra')
    @can('order.add.delivery')
        <a href="{{ route('order.create_delivery', $order) }}" class="btn btn-secondary">@lang('Record Goods Receipt')</a>
    @endcan
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('Order Details')</strong>
    </li>
@endsection

@section('content')
<div class="w-full">
    <div class="row">
        <div class="card w-3/5">
            <div class="card-header">
                <div class="flex">
                    <div>@lang('Order') #{{ $order->internal_order_number }}</div>

                    @can('order.edit')
                    <dot-menu class="ml-2 pt-1">
                        <a href="{{ route('order.edit', $order) }}">@lang('Edit Order')</a>
                    </dot-menu>
                    @endcan
                </div>
            </div>
            <div class="card-content">
            <div class="row">
                <div class="w-1/4">
                    <div class="form-group">
                        <label class="form-label">@lang('internal order number')</label>
                        <div class="form-control-static">{{ $order->internal_order_number }}</div>
                    </div>
                </div>

                <div class="w-1/4">
                    <div class="form-group">
                        <label class="form-label">@lang('Supplier Order Number')</label>
                        <div class="form-control-static">{{ $order->external_order_number }}</div>
                    </div>
                </div>

                <div class="w-1/4">
                    <div class="form-group">
                        <label class="form-label">@lang('Order Date')</label>
                        <div class="form-control-static">{{ !empty($order->order_date) ? $order->order_date->format('d.m.Y') : '' }}</div>
                    </div>
                </div>

                <div class="w-1/4">
                    <div class="form-group">
                        <label class="form-label">
                            @lang('Invoice Number')

                            @can('order.edit')
                                <dot-menu class="ml-2 normal-case order-change-invoice-number">
                                    <a href="javascript:void(0)" class="btn-link" @click="$modal.show('changeInvoiceNumberModal')" id="changeInvoiceNumberLink">@lang('change')</a>
                                </dot-menu>
                            @endcan
                        </label>
                        <div class="form-control-static" dusk="external_invoice_number">{{ $order->external_invoice_number }}</div>
                    </div>
                </div>

            </div>

            <div class="row mt-2 pt-4 border-t border-gray-300">
                <div class="w-1/2">
                    <div class="form-group">
                        <label class="form-label">@lang('Supplier')</label>
                        <div class="form-control-static">
                            <a href="{{ route('supplier.show', $order->supplier) }}" target="_blank" title="@lang('Call up supplier')">{{ optional($order->supplier)->name }}</a>
                            <a href="{{ route('article.index', ['supplier' => $order->supplier->id]) }}" title="@lang('Call up articles of the supplier')"><i class="fa fa-filter"></i></a>
                        </div>
                    </div>
                </div>

                <div class="w-1/4">
                    <div class="form-group order-status">
                        <label class="form-label">
                            @lang('Status')

                            @can('order.edit')
                            <dot-menu class="ml-2 normal-case order-change-status">
                                @foreach(\Mss\Models\Order::getStatusTexts() as $value => $name)
                                    <a href="{{ route('order.change_status', ['order' => $order, 'status' => $value]) }}">{{ $name }}</a>
                                @endforeach
                            </dot-menu>
                            @endcan
                        </label>
                        <div class="form-control-static">
                            @include('order.status', ['status' => $order->status])
                        </div>
                    </div>
                </div>

                <div class="w-1/4">
                    <div class="form-group payment-method">
                        <label class="form-label">
                            @lang('Payment Method')

                            @can('order.edit')
                            <dot-menu class="ml-2 normal-case order-change-payment-method">
                                <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_UNPAID]) }}">@lang('unpaid')</a>
                                <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_PAYPAL]) }}">@lang('Paypal')</a>
                                <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_CREDIT_CARD]) }}">@lang('Credit Card')</a>
                                <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_INVOICE]) }}">@lang('Invoice')</a>
                                <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_AUTOMATIC_DEBIT_TRANSFER]) }}">@lang('Direct Debit')</a>
                                <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_PRE_PAYMENT]) }}">@lang('Prepayment')</a>
                            </dot-menu>
                            @endcan
                        </label>
                        <div class="form-control-static">
                            @if($order->payment_status > 0)
                                <span class="text-success">{{ \Mss\Models\Order::getPaymentStatusText()[$order->payment_status] }}</span>
                            @else
                                <span class="text-danger">{{ \Mss\Models\Order::getPaymentStatusText()[$order->payment_status] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

                <div class="row mt-2 pt-4 border-t border-gray-300">
                    <div class="w-full">
                        <div class="row">
                            <div class="w-full">
                                <div class="form-group">
                                    <label class="form-label">@lang('Notes')</label>
                                    <div class="form-control-static">{{ $order->notes ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-2/5 ml-4">
            <collapse title="@lang('Logbook')">
                @include('components.audit_list', $audits)
            </collapse>
        </div>
    </div>
    <div class="row mt-4">
        <div class="card w-3/5">
            <div class="card-header flex">
                <div class="w-5/12">@lang('Article')</div>
                <div class="w-7/12 flex justify-end">
                    @can('order.edit')
                    <div class="mr-2">
                        {!! Form::open(['method' => 'post', 'route' => ['order.all_items_confirmation_received', $order]]) !!}
                        <button type="submit" class="btn   btn-secondary border-green-600 text-green-600"><z icon="checkmark" class="fill-current w-3 h-3 inline-block"></z> @lang('all order confirmations received')</button>        {!! Form::close() !!}
                    </div>
                    <div>
                        <invoice-status-change-all :order="{{ $order->id }}" :article-has-new-price="{{ $hasOneArticleWithNewPrice ? 'true' : 'false' }}"></invoice-status-change-all>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-content">
                @php ($total = 0)
                @foreach($order->items as $key => $item)
                    @php ($total += ($item->quantity * $item->price))
                    @php ($articleHasNewPrice = ($item->article->getCurrentSupplierArticle()->price / 100) != $item->price)
                    <div class="rounded border border-blue-700 p-4 mb-4" id="order-article-{{ $item->id }}">
                        <div class="row">
                            <div class="w-5/12">
                            <div class="form-group">
                                <label class="form-label">@lang('Article') {{ $key+1 }}</label>
                                <div class="form-control-static">
                                    <a href="{{ route('article.show', $item->article) }}" target="_blank" class="text-sm">{{ $item->article->name }}</a>
                                    <div class="text-xs my-2"># {{ $item->article->internal_article_number }}</div>

                                    @if ($articleHasNewPrice)
                                        <span class="font-semibold text-red-500 text-xs">@lang('Warning, current article price differs from the price in this order!')</span>
                                        <br>
                                    @endif
                                    @if ($item->article->getCurrentSupplierArticle()->supplier_id != $order->supplier_id)
                                        <span class="font-semibold text-red-500 text-xs">@lang('The article now has a different supplier!')</span>
                                        <br>
                                    @endif
                                </div>
                            </div>
                            </div>
                            </div>
                            <div class="w-7/12">
                            <div class="row">
                                <div class="w-4/12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Unit Price Net')</label>
                                        <div class="form-control-static">
                                            {!! formatPrice($item->price)  !!}
                                            @if ($item->quantity > 1)
                                                <div class="text-xs my-2">&sum; {!! formatPrice($item->price * $item->quantity) !!}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="w-4/12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('ordered quantity')</label>
                                        <div class="form-control-static">
                                            {{ $item->quantity }}
                                            @if ($item->quantity > 1)
                                                <br>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="w-4/12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('delivered quantity')</label>
                                        <div class="form-control-static flex @if($item->getQuantityDelivered() < $item->quantity) text-orange-500 @elseif($item->getQuantityDelivered() > $item->quantity) text-red-500 @else text-green-500 @endif">
                                            <div class="flex-1">{{ $item->getQuantityDelivered() }}</div>

                                            @if($item->getQuantityDelivered() == $item->quantity)
                                                <div><z icon="checkmark-outline" class="w-8 h-8 fill-current" title="completely delivered"></z></div>
                                            @elseif($item->getQuantityDelivered() > $item->quantity)
                                                <div><z icon="exclamation-outline" class="w-8 h-8 fill-current" title="too much delivered"></z></div>
                                            @endif

                                            @if ($item->quantity > 1)
                                                <br>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                                <div class="row">
                                    <div class="w-4/12">
                                        <div class="form-group confirmation-status">
                                            <label class="form-label">
                                                @lang('Order Confirmation')

                                                @can('order.edit')
                                                    <dot-menu class="ml-2 normal-case">
                                                        <a href="{{ route('order.item_confirmation_received', ['orderitem' => $item, 'status' => 1]) }}">@lang('received')</a>
                                                        <a href="{{ route('order.item_confirmation_received', ['orderitem' => $item, 'status' => 0]) }}">@lang('not received')</a>
                                                    </dot-menu>
                                                @endcan
                                            </label>
                                            <div class="form-control-static">
                                                @if($item->confirmation_received)
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-green-500"></span>
                                                    <span class="text-green-600 font-semibold align-top">@lang('received')</span>
                                                @else
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-red-500"></span>
                                                    <span class="text-red-600 font-semibold align-top">@lang('not received')</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-4/12">
                                        <div class="form-group invoice-status">
                                            <div class="flex">
                                                <label class="form-label">@lang('Invoice')</label>
                                                @can('order.edit')
                                                    <invoice-status-change :item="{{ $item }}" :article-has-new-price="{{ $articleHasNewPrice ? 1 : 0 }}" invoice-notification-users-count="{{ $invoiceNotificationUsersCount }}" demo="{{ config('app.demo') ? 1 : 0 }}"></invoice-status-change>
                                                @endcan
                                            </div>
                                            <div class="form-control-static">
                                                @if($item->invoice_received === \Mss\Models\OrderItem::INVOICE_STATUS_RECEIVED)
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-green-500"></span>
                                                    <span class="text-green-600 font-semibold align-top">@lang('received')</span>
                                                @elseif($item->invoice_received === \Mss\Models\OrderItem::INVOICE_STATUS_CHECK)
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-orange-500"></span>
                                                    <span class="text-orange-600 font-semibold align-top">@lang('in review')</span>
                                                @else
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-red-500"></span>
                                                    <span class="text-red-600 font-semibold align-top">@lang('not received')</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Delivery Date')</label>
                                            <div class="form-control-static">
                                                {{ !empty($item->expected_delivery) ? $item->expected_delivery->format('d.m.Y') : '' }}
                                                @if($item->expected_delivery && $item->expected_delivery < today() && $item->getQuantityDelivered() < $item->quantity)
                                                    <span class="text-red-600 font-bold text-sm">@lang('overdue')!</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="row">
                    <div class="w-5/12"></div>
                    <div class="w-7/12">
                        <span class="border-t-2 border-gray-800 pt-1">&sum; {!! formatPrice($total) !!}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card w-2/5 ml-4">
            <div class="card-header flex">
                <div class="w-5/12">@lang('Deliveries')</div>
            </div>
            <div class="card-content">
                @foreach($order->deliveries->sortByDesc('delivery_date') as $delivery)
                    <div class="rounded border border-blue-700 p-4 mb-4">
                        <div class="row">
                            <div class="w-4/12">
                                <div class="form-group">
                                    <label class="form-label">@lang('Delivery Date')</label>
                                    <div class="form-control-static">{{ $delivery->delivery_date ? $delivery->delivery_date->format('d.m.Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="w-7/12">
                                <div class="form-group">
                                    <label class="form-label">@lang('Comment')</label>
                                    <div class="form-control-static">{{ $delivery->notes }}</div>
                                </div>
                            </div>
                            <div class="w-1/12 text-right">
                                <dot-menu>
                                    <form action="{{ route('order.delete_delivery', [$order, $delivery]) }}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button class="link" onclick="return confirm('@lang('Are you sure?')')">@lang('Delete')</button>
                                    </form>
                                </dot-menu>
                            </div>
                        </div>
                        <div class="row">
                            <div class="w-full">
                                <table class="table table-condensed table-border">
                                    <thead>
                                    <tr>
                                        <th style="width: 40px">#</th>
                                        <th>@lang('Article')</th>
                                        <th>@lang('Quantity')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($delivery->items as $item)
                                        <tr>
                                            <td>{{ $item->article->internal_article_number }}</td>
                                            <td>
                                                <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="card w-full">
            <div class="card-header flex">
                <div class="flex-1">@lang('Communication')</div>
                @can('ordermessage.create')
                <a href="{{ route('order.message_new', $order) }}" class="btn btn-secondary">@lang('New Message')</a>
                @endcan
            </div>
            <div class="card-content">
                @can('ordermessage.view')
                @include('order.communications')
                @endcan
            </div>
        </div>
    </div>
</div>

<modal name="changeInvoiceNumberModal" height="auto" classes="modal">
    <h4 class="modal-title">@lang('Enter Invoice Number')</h4>

    {!! Form::open(['route' => ['order.set_invoice_number', $order], 'method' => 'POST']) !!}
        <div class="row">
            <div class="w-1/2">
                <div class="form-group">
                    {{ Form::bsText('external_invoice_number', $order->external_invoice_number, [], __('Invoice Number')) }}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" @click="$modal.hide('changeInvoiceNumberModal')">@lang('Cancel')</button>
            <button type="submit" class="btn btn-primary" id="saveInvoiceNumber">@lang('Save')</button>
        </div>
    {!! Form::close() !!}
</modal>

@endsection