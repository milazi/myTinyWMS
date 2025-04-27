@extends('layout.app')

@section('title', __('Dashboard'))

@section('content')

    <div class="flex">
        <div class="card w-1/5 p-4 mb-8">
            <div class="text-sm text-gray-500 font-bold tracking-wide">@lang('Number of Articles')</div>
            <div class="text-3xl font-bold text-black">{{ $stats['article_count'] }}</div>
        </div>

        <div class="card w-1/5 p-4 mb-8 ml-4">
            <div class="text-sm text-gray-500 font-bold tracking-wide">@lang('Total Stock Value')</div>
            <div class="text-3xl font-bold text-black">{!! formatPrice($stats['total_value']) !!}</div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            @lang('To Order')
        </div>
        <div class="card-content">
            {!! Form::open(['route' => ['order.create_post'], 'method' => 'POST']) !!}
            {!! $dataTable->table() !!}
            {!! Form::close() !!}
        </div>
    </div>

    @can('order.create')
    <div class="footer_actions hidden">
        <button class="btn btn-xs btn-secondary" type="submit" id="create_new_order">@lang('Create Order')</button>
    </div>
    @endcan

    <div class="flex flex-wrap -mx-2">
        @if(count($invoicesWithoutDelivery))
        <div class="w-1/2 px-2 mb-4">
            <div class="card">
                <div class="card-header">
                    @lang('Invoices without Goods Receipt')
                </div>
                <div class="card-content">
                    <table class="dataTable">
                        <thead>
                        <tr>
                            <th>@lang('Article')</th>
                            <th>@lang('Order')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoicesWithoutDelivery as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                </td>
                                <td>
                                    <a href="{{ route('order.show', $item->order) }}" target="_blank">{{ $item->order->internal_order_number }}</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if(count($ordersWithoutConfirmation))
            <div class="w-1/2 px-2 mb-4">
                <div class="card">
                    <div class="card-header">
                        @lang('Orders without Order Confirmation')
                    </div>
                    <div class="card-content">
                        <table class="dataTable">
                            <thead>
                            <tr>
                                <th>@lang('Order')</th>
                                <th>@lang('Supplier')</th>
                                <th>@lang('Order Date')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ordersWithoutConfirmation as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('order.show', $order) }}" target="_blank">{{ $order->internal_order_number }}</a>
                                    </td>
                                    <td>{{ optional($order->supplier)->name }}</td>
                                    <td>{{ $order->order_date->format('d.m.Y') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if(count($overdueOrders))
            <div class="w-1/2 px-2 mb-4">
                <div class="card">
                    <div class="card-header">
                        @lang('Overdue Orders')
                    </div>
                    <div class="card-content">
                        <table class="dataTable">
                            <thead>
                            <tr>
                                <th>@lang('Order')</th>
                                <th>@lang('Supplier')</th>
                                <th>@lang('Delivery Date')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($overdueOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('order.show', $order) }}" target="_blank">{{ $order->internal_order_number }}</a>
                                    </td>
                                    <td>{{ $order->supplier->name }}</td>
                                    <td>{{ optional($order->getOldestOverdueDate())->format('d.m.Y') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if(count($deliveriesWithoutInvoice))
        <div class="w-1/2 px-2 mb-4">
            <div class="card">
                <div class="card-header">
                    @lang('Goods Receipts without Invoice')
                </div>
                <div class="card-content">
                    <table class="dataTable">
                        <thead>
                        <tr>
                            <th>@lang('Article')</th>
                            <th>@lang('Order')</th>
                            <th>@lang('Supplier')</th>
                            <th>@lang('Delivery Date')</th>
                            <th>@lang('Order Value')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($deliveriesWithoutInvoice as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                </td>
                                <td>
                                    <a href="{{ route('order.show', $item->order) }}" target="_blank">{{ $item->order->internal_order_number }}</a>
                                </td>
                                <td>{{ $item->order->supplier->name }}</td>
                                <td>
                                    @if($item->deliveryItems->count() > 1)
                                        {{ $item->deliveryItems->first()->created_at->format('d.m.Y') }}
                                    @else
                                        @foreach($item->deliveryItems as $deliveryItem)
                                            {{ $deliveryItem->created_at->format('d.m.Y') }}
                                            @if(!$loop->last)
                                                <br>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td>{!! formatPrice($item->price * $item->quantity) !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if(count($ordersWithoutMessages))
        <div class="w-1/2 px-2 mb-4">
            <div class="card">
                <div class="card-header">
                    @lang('Orders without E-Mail')
                </div>
                <div class="card-content">
                    <table class="dataTable">
                        <thead>
                        <tr>
                            <th>@lang('Order')</th>
                            <th>@lang('Supplier')</th>
                            <th>@lang('Delivery Date')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ordersWithoutMessages as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('order.show', $order) }}" target="_blank">{{ $order->internal_order_number }}</a>
                                </td>
                                <td>{{ $order->supplier->name }}</td>
                                 <td>{{ optional($order->items->max('expected_delivery'))->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        var currentlySelectedSupplier = null;

        $(document).ready(function () {
            $('.dataTable').on("click", 'input[type="checkbox"]', function () {
                if ($('input[name="article[]"]:checked').length === 0) {
                    currentlySelectedSupplier = null;
                    $('.dataTable input[type="checkbox"]').attr('disabled', false).attr('title', '');
                } else {
                    currentlySelectedSupplier = $(this).parent().parent().attr('data-supplier');
                    $('.dataTable tbody tr[data-supplier!=' + currentlySelectedSupplier + '] input[type="checkbox"]').attr('disabled', true).attr('title', 'Only articles from the same supplier can be selected');
                }
            });
        });
    </script>
@endpush
