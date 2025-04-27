@extends('layout.app')

@section('title', __('Article Group').' "'.$articleGroup->name.'"')

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Articles')</a>
    </li>
    <li>
        <a href="{{ route('article-group.index') }}">@lang('Manage Article Groups')</a>
    </li>
    <li class="active">
        <strong>@lang('Show Article Group')</strong>
    </li>
@endsection

@section('content')
<div class="w-full">
    <div class="row">
        <div class="card w-3/5">
            <div class="card-header">
                <div class="flex">
                    <div>@lang('Details')</div>

                    @can('article-group.edit')
                    <dot-menu class="ml-2 pt-1" direction="right" id="edit-group-menu">
                        <a href="{{ route('article-group.edit', $articleGroup) }}" id="edit-group">@lang('Edit Article Group')</a>
                    </dot-menu>
                    @endcan
                </div>
            </div>
            <div class="card-content">
                <div class="row">
                    <div class="w-1/3">
                        <div class="form-group">
                            <label class="form-label">@lang('Name')</label>
                            <div class="form-control-static">{{ $articleGroup->name }}</div>
                        </div>
                    </div>
                    <div class="w-1/3">
                        <div class="form-group">
                            <label class="form-label">@lang('Internal Article Number')</label>
                            <div class="form-control-static">{{ $articleGroup->getArticleNumber() }}</div>
                        </div>
                    </div>
                    <div class="w-1/3">
                        <div class="form-group">
                            <label class="form-label">@lang('External Article Number')</label>
                            <div class="form-control-static">{{ $articleGroup->external_article_number }}</div>
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
                <div class="flex-1">@lang('Articles')</div>

                @can('article.edit')
                    <button type="button" class="btn btn-secondary" @click="$modal.show('change-quantity')">@lang('Change Stock of Articles')</button>
                @endcan
            </div>
            <div class="card-content">
                @foreach($articleGroup->items as $key => $item)
                    <div class="rounded border border-blue-700 p-4 mb-4" id="order-article-{{ $item->id }}">
                        <div class="row">
                            <div class="w-5/12">
                                <div class="form-group">
                                    <label class="form-label">@lang('Article') {{ $key+1 }}</label>
                                    <div class="form-control-static">
                                        <a href="{{ route('article.show', $item->article) }}" target="_blank" class="text-sm">{{ $item->article->name }}</a>
                                        <div class="text-xs my-2"># {{ $item->article->internal_article_number }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-7/12">
                                <div class="row">
                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Supplier')</label>
                                            <div class="form-control-static" id="supplier_{{ $item->id }}">
                                                {{ $item->article->currentSupplier->name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Quantity for this Group')</label>
                                            <div class="form-control-static" id="group_quantity_{{ $item->id }}">
                                                {{ $item->quantity }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Current Stock')</label>
                                            <div class="form-control-static" id="current_quantity_{{ $item->id }}">
                                                {{ $item->article->quantity }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="w-2/5 ml-4"></div>
    </div>
</div>

<modal name="change-quantity" height="auto" width="1000" classes="modal" scrollable="true">
    <change-quantity-article-group-form :article-group="{{ $articleGroup }}"></change-quantity-article-group-form>
</modal>
@endsection