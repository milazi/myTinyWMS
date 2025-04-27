@extends('layout.app')

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

    @yield('form_start')
    <order-form inline-template ref="orderForm" :existing-articles="{{ json_encode($preSetArticles) }}" :supplier-col-id="{{ \Mss\DataTables\SelectArticleDataTable::SUPPLIER_COL_ID }}">
        <div>
            <div class="row">
                <div class="card w-1/2">
                    <div class="card-header">
                        <div>@lang('Order Details')</div>
                    </div>
                    <div class="card-content">
                        <div class="row">
                            <div class="w-1/2 mr-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('internal order number')</label>
                                    <h2 class="form-control-static pt-0 order-number">
                                        {{ $order->internal_order_number }}
                                    </h2>
                                    <small class="text-danger">@lang('Please specify when ordering')</small>
                                </div>
                            </div>
                            <div class="w-1/2">
                                {{ Form::bsSelect('status', $order->status, \Mss\Models\Order::getStatusTexts(),  'Status') }}
                            </div>
                        </div>

                        <div class="row mt-2 pt-4 border-t border-gray-300">
                            <div class="w-1/2 mr-6" id="supplier_select">
                                {{ Form::bsSelect('supplier', $order->supplier_id, \Mss\Models\Supplier::orderedByName()->pluck('name', 'id'),  __('Supplier'), ['placeholder' => '', 'v-model' => 'supplier', 'v-bind:disabled' => 'hasArticles']) }}
                            </div>
                            <div class="w-1/2">
                                {{ Form::bsSelect('payment_status', $order->payment_status, \Mss\Models\Order::getPaymentStatusText(),  __('Payment Method')) }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="w-1/2 mr-6">
                                {{ Form::bsText('external_order_number', null, [], __('Supplier Order Number')) }}
                            </div>

                            <div class="w-1/2">
                                {{ Form::bsText('order_date', (!empty($order->order_date) ? $order->order_date->format('d.m.Y') : ''), ['class' => 'form-control datepicker'], __('Order Date')) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-1/2 ml-4">
                    <div class="card">
                        <div class="card-header">
                            <div>@lang('Notes')</div>
                        </div>
                        <div class="card-content">
                            {{ Form::bsTextarea('notes', null, [], '') }}
                            {!! Form::hidden('order_id', $order->id) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="w-full">
                    <div class="card">
                        <div class="card-header">
                            <h5>@lang('Ordered Articles')</h5>
                        </div>
                        <div class="card-content" id="article-list">
                            <order-article-list ref="articleList" :supplier="supplier" :articles="articles" :all-articles="{{ json_encode($allArticles) }}" :existing-articles="existingArticles"></order-article-list>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="w-full">
                    <div class="card">
                        <div class="card-content">
                            <div class="form-group">
                                @yield('submit')
                            </div>
                        </div>
                    </div>
                </div>
                @yield('secondCol')

                <select-order-article-modal>{!! $dataTable->table() !!}</select-order-article-modal>

                <input type="hidden" name="supplier_id" v-model="supplier">
                <input type="hidden" name="article_data" v-model="articleData">
            </div>
        </div>
    </order-form>
    {!! Form::close() !!}

    <data-tables-filter>
        <data-tables-filter-select label="Category" col-id="{{ \Mss\DataTables\SelectArticleDataTable::CATEGORY_COL_ID }}">
            <option value=""></option>
            @foreach($categories as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </data-tables-filter-select>

        <data-tables-filter-select label="Tags" col-id="{{ \Mss\DataTables\SelectArticleDataTable::TAGS_COL_ID }}">
            <option value=""></option>
            @foreach($tags as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </data-tables-filter-select>

        <data-tables-filter-select label="Status" col-id="{{ \Mss\DataTables\SelectArticleDataTable::STATUS_COL_ID }}" pre-set="1">
            <option value="all">@lang('all')</option>
            <option value="1">@lang('active')</option>
            <option value="0">@lang('deactivated')</option>
            <option value="2">@lang('Order stop')</option>
        </data-tables-filter-select>
    </data-tables-filter>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        function selectArticle(id) {
            window.app.$refs.orderForm.selectArticle(id);
        }
    </script>
@endpush