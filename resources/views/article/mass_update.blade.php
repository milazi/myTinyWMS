@extends('layout.app')

@section('title', __('Article Mass Update'))

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('Article Mass Update')</strong>
    </li>
@endsection

@section('content')
    <form method="post" action="{{ route('article.mass_update_save') }}">
        <div class="row">
        <div class="w-full">
            @foreach($articles as $category => $items)
            <div class="card">
                <div class="card-header">
                    <h5>{{ $category }}</h5>
                </div>
                <div class="card-content">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Article</th>
                                <th width="15%">@lang('Supplier')</th>
                                <th width="25%">@lang('Notes')</th>
                                <th width="10%">@lang('Unit')</th>
                                <th width="10%">@lang('Inventory Type')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $article)
                            <tr>
                                <td>{{ $article->internal_article_number }}</td>
                                <td><a href="{{ route('article.show', $article) }}">{{ $article->name }}</a></td>
                                <td>{{ $article->supplier_name }}</td>
                                <td>{{ $article->notes }}</td>
                                <td>
                                    @if(empty($article->unit_id))
                                        {{ Form::bsSelect('unit_id['.$article->id.']', $article->unit_id, $units,  '', ['placeholder' => '', 'id' => 'unit_'.$article->id]) }}
                                    @else
                                        {{ $article->unit->name }}
                                    @endif
                                </td>
                                <td>
                                    {{ Form::bsSelect('inventory['.$article->id.']', $article->inventory, \Mss\Models\Article::getInventoryTextArray(), '', ['id' => 'inventory_'.$article->id]) }}
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