@extends('layout.app')

@section('title', __('Goods Receipts with Invoice - from :month', ['month' => $start->monthName.' '.$start->year]))

@section('title_extra')
    <a href="{{ route('reports.invoices_with_delivery_export', ['month' => $month, 'category' => $category]) }}" class="btn btn-secondary">@lang('Export as CSV')</a>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('reports.index') }}">@lang('Reports')</a>
    </li>
    <li class="active">
        <strong>@lang('Goods Receipts with Invoice')</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="row">
            <div class="w-full">
                <div class="card">
                    <div class="card-content">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Order')</th>
                                    <th>@lang('Article')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Supplier')</th>
                                    <th>@lang('Delivery Date')</th>
                                    <th>@lang('Order Value')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($grandTotal = 0)
                                @foreach($items as $deliveriesGroup)
                                <tr>
                                    <td>
                                        <a href="{{ route('order.show', $deliveriesGroup->first()->order) }}" target="_blank">{{ $deliveriesGroup->first()->order->internal_order_number }}</a>
                                    </td>
                                    <td>
                                        @foreach($deliveriesGroup as $delivery)
                                            @foreach($delivery->items as $deliveryItem)
                                                {{ $deliveryItem->article->name }}<br>
                                            @endforeach
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($deliveriesGroup as $delivery)
                                            @foreach($delivery->items as $deliveryItem)
                                                {{ $deliveryItem->article->category->name }}<br>
                                            @endforeach
                                        @endforeach
                                    </td>
                                    <td>{{ $deliveriesGroup->first()->order->supplier->name }}</td>
                                    <td>
                                        @foreach($deliveriesGroup as $delivery)
                                            @foreach($delivery->items as $deliveryItem)
                                                {{ $delivery->created_at->format('d.m.Y') }}<br>
                                            @endforeach
                                        @endforeach
                                    </td>
                                    <td class="text-right">
                                        @php($total = 0)
                                        @php($itemCount = 0)
                                        @foreach($deliveriesGroup as $delivery)
                                            @foreach($delivery->items as $deliveryItem)
                                                {!! formatPrice($deliveryItem->orderItem->price * $deliveryItem->orderItem->quantity) !!}<br>
                                                @php($total += $deliveryItem->orderItem->price * $deliveryItem->orderItem->quantity)
                                                @php($itemCount++)
                                            @endforeach
                                        @endforeach
                                        @if($itemCount > 1)
                                            <span class="font-bold border-black border-t-2">&sum; {!! formatPrice($total) !!}</span>
                                        @endif
                                        @php($grandTotal += $total)
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right" colspan="6">{!! formatPrice($grandTotal) !!}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection