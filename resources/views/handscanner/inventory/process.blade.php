@extends('layout.handscanner')

@section('subheader')
    Inventory - Entry
@endsection

@section('back', route('handscanner.inventory.select_article', [$inventory, $article->category]))

@section('content')
    @if (!$article->category->is($category))
        <div class="alert alert-secondary">Warning, article is from a different category!</div>
    @endif

    @if(!is_null($item->processed_at))
        <div class="alert alert-warning">Warning, article has already been processed!</div>
    @endif
    <div class="row">
        <div class="col">
            <form method="post" action="{{ route('handscanner.inventory.processed', [$inventory, $article]) }}" id="saveinventory">
                @csrf
                <div class="row text-left">
                    <div class="col">
                        <div class="label">Name:</div>
                        <h6>{{ $article->name }}</h6>
                    </div>
                </div>

                <div class="row text-left">
                    <div class="col-4">
                        <div class="label">Number:</div>
                        <h5>{{ $article->internal_article_number }}</h5>
                    </div>

                    <div class="col-8">
                        <div class="label">current stock:</div>
                        <h5>{{ $article->quantity }} {{ optional($article->unit)->name }}</h5>
                    </div>
                </div>

                <div class="row text-left">
                    @if ($article->outsourcing_quantity !== 0)
                        <div class="col">
                            <div class="label text-danger">External warehouse stock:</div>
                            <h5 class="text-danger">{{ $article->outsourcing_quantity }}</h5>
                        </div>
                    @endif

                    @if ($article->replacement_delivery_quantity !== 0)
                        <div class="col">
                            <div class="label text-danger">Replacement delivery:</div>
                            <h5 class="text-danger">{{ $article->replacement_delivery_quantity }}</h5>
                        </div>
                    @endif
                </div>

                <div class="row text-left">
                    <div class="col">
                        <div class="form-group">
                            <label for="quantity">new stock:</label>
                            <input type="number" min="0" inputmode="numeric" pattern="[0-9]*" name="quantity" id="quantity" required class="form-control form-control-lg">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-lg btn-success" id="changelogSubmit">Save</button>
                <a href="{{ route('handscanner.inventory.select_article', [$inventory, $article->category]) }}" class="btn btn-lg btn-secondary pull-right">Cancel</a>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col text-center">
            <form method="post" action="{{ route('handscanner.inventory.processed', [$inventory, $article]) }}">
                @csrf
                <br>
                <br>
                <input type="hidden" name="quantity" value="{{ $article->quantity }}">
                <button type="submit" class="btn btn-lg btn-primary">Stock is correct</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#saveinventory').submit(function () {
                var check = window.confirm('Change stock to ' + $('#quantity').val() + '?');
                console.log(check);
                return check;
            });
        });
    </script>
@endpush