@extends('layout.handscanner')

@section('content')
    <h3 class="text-center m-b-md m-t-xs">Login</h3>

    <form method="post" action="{{ route('handscanner.processlogin') }}">

        <div class="form-group">
            <label for="user">Select User</label>
            <select class="form-control form-control-lg" name="user" id="user">
                @foreach(\Mss\Models\UserSettings::getUsersWhereHas(\Mss\Models\UserSettings::SETTINGS_HANDSCANNER_PIN_CODE)->sortBy('name') as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="pin">PIN Code</label>
            <input type="number" class="form-control form-control-lg m-b-lg text-center" name="pin" id="pin">
        </div>
        <button class="btn btn-primary btn-lg btn-block">Login</button>
        {{ csrf_field() }}
    </form>
@endsection