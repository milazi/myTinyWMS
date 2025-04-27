@extends('layout.app')

@section('content')
@if ($isNewArticle || $isCopyOfArticle)
    @yield('form_start')
@endif
<div class="flex w-full items-start">
    <div class="card 2xl:w-1/3 w-1/2">
        <div class="card-header flex">
            <div class="flex-1">@lang('Details')</div>

            <dot-menu class="ml-2">
                @if (!$isNewArticle && !$isCopyOfArticle && Auth::user()->can('article.create'))
                    <a href="{{ route('article.copy', $article) }}" class="btn-link" id="copyArticleLink">@lang('Duplicate article')</a>
                @endif
                @if(!$isNewArticle && !$isCopyOfArticle)
                    <a href="{{ route('article.delete', $article) }}" class="btn-link" onclick="return confirm('Wirklich löschen?')" id="deleteArticleLink">@lang('Delete article')</a>
                @endif
            </dot-menu>
        </div>

        <div class="card-content">
            @if (!$isNewArticle && !$isCopyOfArticle)
                @yield('form_start')
            @endif

            <div class="w-full flex pb-4">
                <div class="w-1/3">
                    @if (!$isNewArticle && !$isCopyOfArticle)
                        <div class="form-group">
                            <label class="form-label">@lang('Internal article number')</label>

                            <div class="form-control-static">
                                {{ $article->internal_article_number }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="w-2/3">
                    {{ Form::bsText('external_article_number', $article->external_article_number, ['placeholder' => __('Shop article number, SKU, EAN, ...')], __('External article number')) }}
                </div>
            </div>

            <div class="w-full flex">
                <div class="w-1/3">
                    @if (!$isNewArticle && !$isCopyOfArticle)
                        <div class="form-group">
                            <label class="form-label">@lang('Stock')</label>

                            <div class="form-control-static" id="currentQuantity">
                                {{ $article->quantity }} @can('article.change_quantity')<button type="button" class="btn-link btn-xs edit-quantity" @click="$modal.show('change-quantity')">@lang('change')</button>@endcan
                            </div>
                        </div>
                    @endif
                </div>

                <div class="w-2/3">
                    @if($article->openOrders()->count() && !$isCopyOfArticle)
                        <div class="form-group">
                            <label class="form-label">@lang('Open orders')</label>
                            <div class="form-control-static text-sm">
                                @foreach($article->openOrders() as $openOrder)
                                    <a href="{{ route('order.show', $openOrder) }}" target="_blank">{{ $openOrder->internal_order_number }}</a> ({{ $openOrder->items->where('article_id', $article->id)->first()->quantity }}{{ !empty($article->unit) ? ' '.$article->unit->name : '' }})
                                    <br>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if (!$isNewArticle && !$isCopyOfArticle && ($article->outsourcing_quantity !== 0 || $article->replacement_delivery_quantity !== 0))
                <div class="row">
                    @if ($article->outsourcing_quantity !== 0)
                        <div class="w-1/3">
                            <div class="form-group">
                                <label class="form-label">@lang('External warehouse stock')</label>
                                <div class="form-control-static">
                                    {{ $article->outsourcing_quantity }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($article->replacement_delivery_quantity !== 0)
                        <div class="w-1/3">
                            <div class="form-group">
                                <label class="form-label">@lang('Replacement delivery')</label>
                                <div class="form-control-static">
                                    {{ $article->replacement_delivery_quantity }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{ Form::bsTextarea('name', $article->name, ['rows' => 2] , __('Name')) }}
            {{ Form::bsSelect('status', $article->status, \Mss\Models\Article::getStatusTextArray(),  __('Status')) }}
            {{ Form::bsText('tags', $article->tags->pluck('name')->implode(', '), ['class' => ''], __('Tags')) }}

            <div class="form-group">
                {!! Form::label('category', __('Category'), ['class' => 'form-label inline-block']) !!}
                <a href="{{ route('article.index', ['category' => $article->category]) }}" class="ml-2" title="@lang('show all articles of this category')" target="_blank"><i class="fa fa-filter"></i></a>

                @if ($isNewArticle || $isCopyOfArticle)
                    {!! Form::select('category', \Mss\Models\Category::orderedByName()->pluck('name', 'id'), $isCopyOfArticle ? $article->category_id : null, ['class' => 'form-select w-full', 'name' => 'category', 'id' => 'changeArticleCategory', 'placeholder' => __('please choose ...')]) !!}
                @else
                    {!! Form::select('category', \Mss\Models\Category::orderedByName()->pluck('name', 'id'), $article->category->id ?? null, ['class' => 'form-select w-full', 'name' => 'category', 'disabled' => 'disabled']) !!}
                    <div class="i-checks mt-2">
                        <label>
                            <input type="checkbox" id="enableChangeCategory" name="changeCategory" value="1" />
                            @lang('Change category')
                        </label>
                    </div>
                    <span class="help-block text-red-500 mt-2 hidden" id="changeCategoryWarning">@lang('When changing the category, a new article number will be assigned!')</span>
                @endif
            </div>

            <div class="row">
                <div class="w-1/2 pr-4">
                    @if (!empty($article->unit_id) && !$isCopyOfArticle)
                        <div class="form-group">
                            <label class="form-label">@lang('Unit')</label>
                            <div class="form-control-static">{{ $article->unit->name }}</div>
                        </div>
                    @else
                        {{ Form::bsSelect('unit_id', $article->unit_id, \Mss\Models\Unit::pluck('name', 'id'),  __('Unit'), ['required' => 'required', 'placeholder' => __('please choose ...')]) }}
                    @endif
                </div>
                <div class="w-1/2">
                    {{ Form::bsText('sort_id', $article->sort_id ?? 0, ['required' => 'required'], __('Sorting')) }}
                </div>
            </div>

            <div class="row">
                <div class="w-1/2 pr-4">
                    @if ($isNewArticle || $isCopyOfArticle)
                        <div class="form-group">
                            <label class="control-label">@lang('Stock')</label>
                            <div class="form-control-static text-red-600 text-sm">
                                <input type="hidden" name="quantity" value="0" />
                                @lang('Initial stock can only be set via a GR')
                            </div>
                        </div>
                    @endif
                </div>
                <div class="w-1/2">

                </div>
            </div>

            <div class="row">
                <div class="w-1/2 pr-4">
                    {{ Form::bsText('min_quantity', $article->min_quantity ?? 0, [], __('Minimum stock')) }}
                </div>
                <div class="w-1/2">
                    {{ Form::bsText('issue_quantity', $article->issue_quantity ?? 0, [], __('Withdrawal quantity')) }}
                </div>
            </div>

            <div class="row">
                <div class="w-1/2 pr-4">
                    {{ Form::bsSelect('inventory', $article->inventory, \Mss\Models\Article::getInventoryTextArray(),  __('Inventory Type'), ['required' => 'required', 'placeholder' => __('please choose ...')]) }}
                </div>
                <div class="w-1/2">
                    {{ Form::bsText('free_lines_in_printed_list', $article->free_lines_in_printed_list ?? 1, [], __('Free lines in stock list')) }}
                </div>
            </div>

            <div class="row">
                <div class="w-1/2 pr-4">
                    {{ Form::bsText('cost_center', $article->cost_center ?? '', [], __('Cost center')) }}
                </div>
                <div class="w-1/2">

                </div>
            </div>

            <div class="row">
                <div class="w-1/2 pr-4">
                    {{ Form::bsSelect('packaging_category', $article->packaging_category, [null => '', \Mss\Models\Article::PACKAGING_CATEGORY_PAPER => __('Paper, cardboard, carton'), \Mss\Models\Article::PACKAGING_CATEGORY_PLASTIC => __('Plastics'), \Mss\Models\Article::PACKAGING_CATEGORY_METAL => __('Sheet metal/iron/metal')],  __('Packaging Category')) }}
                </div>
                <div class="w-1/2">
                    {{ Form::bsText('weight', $article->weight ?? '', [], __('Weight in grams per unit')) }}
                </div>
            </div>

            {{ Form::bsTextarea('notes', $article->notes, ['rows' => 4], __('Notes')) }}
            {{ Form::bsTextarea('order_notes', $article->order_notes, ['rows' => 2], __('Order Notes')) }}
            {{ Form::bsTextarea('delivery_notes', $article->delivery_notes, ['rows' => 2], __('Delivery/GR Notes')) }}

            @yield('submit')

            @if (!$isNewArticle && !$isCopyOfArticle)
                {!! Form::close() !!}
            @endif
        </div>
    </div>

    @yield('secondCol')

    @if ($isNewArticle || $isCopyOfArticle)
        {!! Form::close() !!}
    @endif
</div>

<modal name="change-quantity" height="auto" classes="modal">
    <change-quantity-form :article="{{ $article }}" unit="{{ optional($article->unit)->name }}"></change-quantity-form>
</modal>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        @if (!$isNewArticle && !$isCopyOfArticle)
        $('#unit_id').change(function () {
            alert(__('Warning. Changing the unit only in consultation with accounting or management!'))
        });
        @endif


        $('#enableChangeCategory').on('ifChecked', function(event){
            $('#changeCategoryWarning').removeClass('hidden');
            $("#category").prop('disabled', false).removeClass('form-disabled');
        });

        $('#enableChangeCategory').on('ifUnchecked', function(event){
            $('#changeCategoryWarning').addClass('hidden');
            $("#category").prop('disabled', true).addClass('form-disabled');
        });

        $('#tags')
            .tagify({
                whitelist: [
                    @foreach(\Mss\Models\Tag::orderedByName()->pluck('name', 'id') as $id => $name)
                    {"id": {{ $id }}, "value": "{{ $name }}"},
                    @endforeach
                ],
                dropdown : {
                    classname : "form-select",
                    enabled   : 3,
                    maxItems  : 5
                }
            })
            .on('add', function(e, tagName){
                console.log('added', tagName)
            });

        $("#category").select2({
            theme: "default",
            dropdownCssClass: 'form-select',
            containerCssClass: 'form-control-static',
        });
    });
</script>
@endpush