@extends('layout.app')

@section('title', __('New Goods Receipt'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Overview')</a>
    </li>
    <li>
        <a href="{{ route('order.show', $order) }}">@lang('Order') #{{ $order->internal_order_number }}</a>
    </li>
    <li class="active">
        <strong>@lang('New Goods Receipt')</strong>
    </li>
@endsection

@section('content')
    @if (count($errors) > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {!! Form::open(['route' => ['order.store_delivery', $order], 'method' => 'POST']) !!}
    <div class="row">
        <div class="w-full">
            <div class="card w-1/2">
                <div class="card-header">
                    <h5>@lang('New Goods Receipt')</h5>
                </div>
                <div class="card-content">
                    <div class="row">
                        <div class="w-1/2">
                            {{ Form::bsText('delivery_note_number', null, [], __('Delivery Note Number')) }}
                        </div>

                        <div class="w-1/2 pl-6">
                            {{ Form::bsText('delivery_date', \Carbon\Carbon::now()->format('d.m.Y'), ['class' => 'form-control datepicker', 'data-date-end-date' => '0d'], __('Delivery Date')) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-full">
                            {{ Form::bsTextarea('notes', null, ['rows' => 3], __('Notes')) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>@lang('Delivered Articles')</h5>
                </div>
                <div class="card-content">
                    @foreach($order->items as $key => $item)
                        <div class="rounded border border-blue-700 p-4 mb-4 row">
                            <div class="w-4/12">
                                <small class="form-label">@lang('Article') {{ $key+1 }}</small>
                                <h4>
                                    <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                    <br/>
                                    <small class="p-t-8"># {{ $item->article->internal_article_number }}</small>
                                    @if(!empty($item->article->delivery_notes))
                                        <br>
                                        <span class="font-semibold text-red-500 text-xs">{{ $item->article->delivery_notes }}</span>
                                    @endif
                                </h4>
                            </div>
                            <div class="w-2/12">
                                <small class="form-label">@lang('Order Number')</small>
                                <h4>{{ $item->article->currentSupplierArticle->order_number }}</h4>
                            </div>
                            <div class="w-1/12">
                                <small class="form-label">@lang('ordered quantity')</small>
                                <h4>{{ $item->quantity }}</h4>
                            </div>
                            <div class="w-1/12">
                                <small class="form-label">@lang('already delivered')</small>
                                <h4>{{ $item->getQuantityDelivered() }}</h4>
                            </div>
                            <div class="w-2/12 mr-2">
                                <small class="form-label">@lang('delivered quantity')</small>
                                <div class="mt-2 flex">
                                    <input class="form-input mr-2" type="number" min="0" name="quantities[{{ $item->article->id }}]">
                                    <div class="">
                                        <button type="button" class="btn btn-success set-full-quantity" dusk="set-full-quantity-{{ $item->id }}" data-quantity="{{ ($item->quantity - $item->getQuantityDelivered() > 0) ? ($item->quantity - $item->getQuantityDelivered()) : 0 }}" title="all"><i class="fa fa-check"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="w-2/12 mr-2">
                                <small class="form-label">@lang('Print Label')</small>
                                <div class="row mt-2">
                                    <div class="w-4/12 mr-2">
                                        <div class="input-group">
                                            <input class="form-input" type="text" name="label_count[{{ $item->article->id }}]" value="0">
                                        </div>
                                    </div>
                                    <div class="w-8/12">
                                        <div class="input-group has-feedback">
                                            <select class="form-select pl-2" name="label_type[{{ $item->article->id }}]">
                                                <option value="small">@lang('Small')</option>
                                                <option value="large">@lang('Large')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-content">
                    <div class="form-group">
                        {!! Form::submit(__('Save'), ['class' => 'btn btn-primary force-inline', 'id' => 'save-delivery']) !!}
                    </div>
                </div>
            </div>
        </div>
        @yield('secondCol')
    </div>
    {!! Form::close() !!}
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.set-full-quantity').click(function () {
                $(this).parent().parent().find('input').val($(this).attr('data-quantity'));
            });

            $('.datepicker').datepicker({
                format: 'dd.mm.yyyy',
                language: 'en', // Changed to 'en' for English
                todayHighlight: true,
                daysOfWeekDisabled: [0,6],
                autoclose: true
            });
        })
    </script>
@endpush