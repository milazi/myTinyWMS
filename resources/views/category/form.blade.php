@extends('layout.app')

@section('content')
    @yield('form_start')
    <div class="w-full flex">
        <div class="w-1/3">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('Details')</h5>
                </div>
                <div class="card-content">
                    {{ Form::bsText('name', null, [], __('Name')) }}
                    {{ Form::bsTextarea('notes', null, [], __('Notes')) }}

                    {{ __('Show in "To Order" list on Dashboard') }}
                    <div class="radio">
                        <label>
                            {{ Form::radio('show_in_to_order_on_dashboard', 1, null) }}
                            @lang('Yes')
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {{ Form::radio('show_in_to_order_on_dashboard', 0, null) }}
                            @lang('No')
                        </label>
                    </div>

                    <div class="form-group mt-4">
                        @yield('submit')
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        @yield('secondCol')
    </div>
@endsection