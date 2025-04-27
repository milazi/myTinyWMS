@extends('layout.handscanner')

@section('subheader')
    <div class="subheader">Inventory Step 1</div>
@endsection

@section('back', route('handscanner.index'))

@section('content')
    <div class="jumbotron text-center">Please scan an article</div>

    <qr-reader target-url="{{ route('handscanner.inventory.step2', ['articleNumber' => '']) }}/" style="height: 200px; width: 200px;"></qr-reader>

@endsection