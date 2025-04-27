@extends('layout.handscanner')

@section('subheader')
    <div class="subheader">Inventory Start</div>
@endsection

@section('back', route('handscanner.index'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h5 class="text-center mb-4 mt-2">Please select</h5>
            <a href="{{ route('handscanner.inventory.new') }}" class="btn btn-lg btn-block btn-primary m-b-lg">New Inventory</a>

            @if($inventories->count())
            <hr class="bg-light"/>

            <form method="post" action="{{ route('handscanner.inventory.continue') }}">
                @csrf
                <div class="form-group">
                    <label for="user">Continue Inventory</label>
                    <select class="form-control form-control-lg" name="inventory" id="inventory">
                        @foreach($inventories as $inventory)
                            <option value="{{ $inventory->id }}">started on {{ $inventory->created_at->format('m/d/Y H:i') }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-lg btn-block btn-primary m-b-lg">Continue</button>
            </form>
            @endif
        </div>
    </div>
@endsection