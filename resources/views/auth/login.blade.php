@extends('layout.loginregister')

@section('content')
    @if(env('APP_DEMO'))
        <div class="w-full max-w-lg bg-white border border-red-400 shadow-md rounded-lg px-8 pt-6 pb-8 mb-12">
            <b>@lang('DEMO MODE')</b>
            <br>
            <br>
            @lang('Username and password are pre-filled.')<br>
            @lang('Just log in.')<br>
            <br>
            @lang('The demo will be reset every 24 hours.')<br>
            <br>
            @lang('No emails will be sent in demo mode.')
        </div>
    @endif


    <div class="w-full max-w-sm">
        <h2 class="text-center mb-4">@lang('Welcome to') {{ env('APP_NAME') }}</h2>

        <form class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4" action="{{ route('login') }}" method="post">
            {{ csrf_field() }}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="login">
                    @lang('Username / E-mail')
                </label>
                <input class="form-input {{ $errors->has('username') || $errors->has('email') ? ' has-error' : '' }}" id="login" name="login" value="{{ env('APP_DEMO') ? 'admin@example.com' : old('login') }}" type="text" placeholder="" required>

                @if ($errors->has('username'))
                    <p class="text-red-500 text-xs italic mt-2">{{ $errors->first('username') }}</p>
                @endif
                @if ($errors->has('email'))
                    <p class="text-red-500 text-xs italic mt-2">{{ $errors->first('email') }}</p>
                @endif
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    @lang('Password')
                </label>
                <input class="form-input {{ $errors->has('password') ? ' has-error' : '' }}" value="{{ env('APP_DEMO') ? 'password' : '' }}" id="password" name="password" type="password" placeholder="" required>

                @if ($errors->has('password'))
                    <p class="text-red-500 text-xs italic mt-2">{{ $errors->first('password') }}</p>
                @endif
            </div>
            <div class="flex items-center justify-between">
                <button class="btn btn-primary" type="submit">
                    @lang('Login')
                </button>
                <a class="btn-link text-sm" href="{{ route('password.request') }}">
                    @lang('Forgot your password?')
                </a>
            </div>
        </form>
    </div>
@endsection