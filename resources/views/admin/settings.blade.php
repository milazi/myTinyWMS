@extends('layout.app')

@section('title', __('Settings'))

@section('content')

<form method="post" action="{{ route('admin.settings.save') }}">
    <div class="w-full">
        <div class="card 2xl:w-2/3 w-full mb-6">
            <div class="card-content flex">
                <div class="w-1/2 pt-4">
                    <div class="text-lg pb-2">@lang('Outgoing E-mails')</div>
                    <p class="text-sm text-gray-700 pr-20">
                        @lang('Define here the SMTP server that should be used for outgoing e-mails.')
                    </p>
                </div>
                <div class="w-1/2 pt-4">
                    <div class="row">
                        <div class="w-2/3 pr-4">
                            {{ Form::bsText('smtp_host', settings('smtp.host'), [], __('Host')) }}
                        </div>
                        <div class="w-1/3 pr-4">
                            {{ Form::bsText('smtp_port', settings('smtp.port'), [], __('Port')) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsText('smtp_username', !empty(settings('smtp.username')) ? decrypt(settings('smtp.username')) : '', [], __('Username')) }}
                        </div>
                        <div class="w-1/2 pr-4">
                            <password-field value="{{ old('smtp_password', !empty(settings('smtp.password')) ? decrypt(settings('smtp.password')) : '') }}" id="smtp_password" name="smtp_password" label="@lang('Password')"></password-field>
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsSelect('smtp_encryption', settings('smtp.encryption'), ['tls' => __('TLS'), 'ssl' => __('SSL')],  __('Encryption'), ['placeholder' => __('None')]) }}
                        </div>
                        <div class="w-1/2 pr-4">

                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsText('smtp_from_address', settings('smtp.from_address'), [], __('Sender Address')) }}
                        </div>
                        <div class="w-1/2 pr-4">
                            {{ Form::bsText('smtp_from_name', settings('smtp.from_name'), [], __('Sender Name')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card 2xl:w-2/3 w-full mb-6">
            <div class="card-content flex">
                <div class="w-1/2 pt-4">
                    <div class="text-lg pb-2">@lang('Import of incoming e-mails for orders')</div>
                    <p class="text-sm text-gray-700 pr-20">
                        @lang('myTinyWMS can automatically import incoming e-mails and assign them to orders. If you want to use this feature, please enter the IMAP access data below.')
                    </p>
                </div>
                <div class="w-1/2 pt-4">
                    <div class="row">
                        <div class="w-full pb-4">
                            {{ Form::bsCheckbox('imap_enabled', 1, __('Automatically import e-mails'), settings('imap.enabled', false)) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-2/3 pr-4">
                            {{ Form::bsText('imap_host', settings('imap.host'), [], __('Host')) }}
                        </div>
                        <div class="w-1/3 pr-4">
                            {{ Form::bsText('imap_port', settings('imap.port'), [], __('Port')) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsText('imap_username', !empty(settings('imap.username')) ? decrypt(settings('imap.username')) : '', [], __('Username')) }}
                        </div>
                        <div class="w-1/2 pr-4">
                            <password-field value="{{ old('imap_password', !empty(settings('imap.password')) ? decrypt(settings('imap.password')) : '') }}" id="imap_password" name="imap_password" label="@lang('Password')"></password-field>
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsSelect('imap_encryption', settings('imap.encryption'), ['tls' => __('TLS'), 'ssl' => __('SSL')],  __('Encryption'), ['placeholder' => __('None')]) }}
                        </div>
                        <div class="w-1/2 pr-4">

                        </div>
                    </div>
                    <div class="row">
                        <div class="w-full pb-4">
                            {{ Form::bsCheckbox('imap_delete', 1, __('Delete e-mails from the server after import'), settings('imap.delete', false)) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card 2xl:w-2/3 w-full">
        <div class="card-content">
            @csrf
            {!! Form::submit(__('Save'), ['class' => 'btn btn-primary', 'id' => 'saveSettings']) !!}
        </div>
    </div>
</form>
@endsection