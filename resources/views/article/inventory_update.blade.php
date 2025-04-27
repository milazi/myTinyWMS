@extends('layout.app')

@section('title', __('Article Inventory Update'))

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('Article Inventory Update')</strong>
    </li>
@endsection

@section('content')
    <form method="post" action="{{ route('article.inventory_update_save') }}">
        <div class="row">
            <div class="w-full">
                <div class="alert alert-danger">@lang('Warning, changes to the stock will be saved here as an inventory booking!')</div>
                @php $tabindex = 0; @endphp
                @foreach($articles as $category => $items)
                    <div class="card mt-6">
                        <div class="card-header">
                            <h5>{{ $category }}</h5>
                        </div>
                        <div class="card-content">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('Article')</th>
                                        <th width="15%">@lang('Supplier')</th>
                                        <th width="25%">@lang('Notes')</th>
                                        <th width="10%">@lang('Inventory Type')</th>
                                        <th width="10%">@lang('Unit')</th>
                                        <th width="10%">@lang('current stock')</th>
                                        <th width="10%">@lang('new stock')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $article)
                                        @php $tabindex++; @endphp
                                        <tr>
                                            <td>{{ $article->internal_article_number }}</td>
                                            <td><a href="{{ route('article.show', $article) }}">{{ $article->name }}</a></td>
                                            <td>{{ $article->supplier_name }}</td>
                                            <td>{{ $article->notes }}</td>
                                            <td>{{ \Mss\Models\Article::getInventoryTextArray()[$article->inventory] }}</td>
                                            <td>{{ optional($article->unit)->name }}</td>
                                            <td>{{ $article->quantity }}</td>
                                            <td data-org-quantity="{{ $article->quantity }}">
                                                {{ Form::bsText('quantity['.$article->id.']', $article->quantity, ['class' => 'form-input newquantity', 'tabindex' => $tabindex, 'id' => 'quantity_'.$article->id], '') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
                <div class="card mt-4">
                    <div class="card-content">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary" id="submit">@lang('Save')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.newquantity').change(function () {
            if (parseInt($(this).attr('data-org-quantity')) !== parseInt($(this).val())) {
                $(this).addClass('bg-red-500').addClass('text-white');
            } else {
                $(this).removeClass('bg-red-500').removeClass('text-white');
            }
        });
    });
</script>
@endpush