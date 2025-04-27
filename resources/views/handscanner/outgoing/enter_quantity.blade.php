@extends('layout.handscanner')

@section('subheader')
    <div class="subheader">Goods Issue - Change Stock</div>
@endsection

@section('back', route('handscanner.index'))

@section('content')
    <div class="row">
        <div class="col">
            <form method="post" action="{{ route('handscanner.outgoing.save', [$article]) }}" id="saveoutgoing">
                @csrf
                <div class="row text-left">
                    <div class="col">
                        <div class="label">Name:</div>
                        <h6>{{ $article->name }}</h6>
                    </div>
                </div>

                <div class="row text-left">
                    <div class="col">
                        <div class="label">Number:</div>
                        <h5>{{ $article->internal_article_number }}</h5>
                    </div>

                    <div class="col">
                        <div class="label">current stock:</div>
                        <h5>{{ $article->quantity }}</h5>
                    </div>
                </div>

                <div class="row text-left">
                    <div class="col">
                        <div class="label">Unit:</div>
                        <h5>{{ optional($article->unit)->name }}</h5>
                    </div>
                </div>

                <div class="row text-left">
                    <div class="col">
                        <div class="form-group">
                            <label for="quantity">How many articles should be booked out:</label>
                            <input type="number" min="0" inputmode="numeric" pattern="[0-9]*" name="quantity" id="quantity" required class="form-control form-control-lg">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-lg btn-success" id="changelogSubmit">Save</button>
                <a href="{{ route('handscanner.outgoing.start') }}" class="btn btn-lg btn-secondary pull-right">Cancel</a>
            </form>
        </div>
    </div>
@endsection