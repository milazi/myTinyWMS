@extends('layout.app')

@section('title', __('Settings'))

@section('summernote_custom_config')
    ,height: 500
@endsection

@section('content')
<form method="post" action="{{ route('settings.save') }}">
    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('General')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4">
                <div class="">
                    <label for="lang" class="form-label">@lang('Language')</label>
                    <select class="form-input w-32" id="lang" name="setting[{{ UserSettings::SETTINGS_LANGUAGE }}]">
                        <option value="de" {{ Auth::user()->settings()->get(UserSettings::SETTINGS_LANGUAGE) == 'de' ? 'selected="selected"' : '' }}>@lang('German')</option>
                        <option value="en" {{ Auth::user()->settings()->get(UserSettings::SETTINGS_LANGUAGE) == 'en' ? 'selected="selected"' : '' }}>@lang('English')</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('Notifications')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4">
                <div class="mb-4">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED)) checked @endif value="1">
                            @lang('Notification when a delivery is made for an article for which an invoice already exists')
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_WITHOUT_INVOICE_RECEIVED }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_WITHOUT_INVOICE_RECEIVED)) checked @endif value="1">
                            @lang('Notification when a delivery is made for an article for which no invoice yet exists')
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS)) checked @endif value="1">
                            @lang('Notification for invoices marked for review')
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_DELIVERY_QUANTITY_DIFFERS_FROM_ORDER_QUANTITY }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_DELIVERY_QUANTITY_DIFFERS_FROM_ORDER_QUANTITY)) checked @endif value="1">
                            @lang('Notification for differing delivery quantity')
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_ABOUT_CORRECTION_ON_CHANGE_OF_OTHER_MONTH }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_ABOUT_CORRECTION_ON_CHANGE_OF_OTHER_MONTH)) checked @endif value="1">
                            @lang('Notification for correction postings to changes from another month')
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="select1" class="form-label">@lang('Notify when a delivery is received for an article in the selected category.')</label>
                    <select class="w-1/2 select2" id="select1" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES }}][]" multiple="multiple">
                        @foreach(\Mss\Models\Category::all() as $category)
                            <option value="{{ $category->id }}" @if(in_array($category->id, Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES))) selected @endif>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('Hand Scanner')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4">
                <div>
                    <label for="select1" class="form-label">@lang('PIN Code for Hand Scanner')</label>
                    <input type="text" class="form-input w-32" name="setting[{{ UserSettings::SETTINGS_HANDSCANNER_PIN_CODE }}]" value="{{ Auth::user()->settings()->get(UserSettings::SETTINGS_HANDSCANNER_PIN_CODE) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('Email Settings')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4 pr-2">
                {{ Form::wysiwygEditor('signature', $signature, [], __('Email Signature')) }}
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('API Tokens')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4">
                <table class="dataTable">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Permissions')</th>
                            <th>@lang('Created')</th>
                            <th>@lang('Last Used')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\Illuminate\Support\Facades\Auth::user()->tokens as $token)
                            <tr class="{{ ($loop->even) ? 'even' : ''}}">
                                <td>{{ $token->name }}</td>
                                <td>{{ implode(', ', $token->abilities) }}</td>
                                <td>{{ $token->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ optional($token->last_used_at)->format('d.m.Y H:i') }}</td>
                                <td><a href="{{ route('settings.remove_token', $token) }}">@lang('Delete Token')</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-left">@lang('No tokens available')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <button type="button" @click="$modal.show('newTokenModal')" class="btn btn-secondary ml-4 mt-8">@lang('Create Token')</button>
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full">
        <div class="card-content">
            @csrf
            {!! Form::submit(__('Save'), ['class' => 'btn btn-primary', 'id' => 'saveSettings']) !!}
        </div>
    </div>
</form>

<modal name="newTokenModal" height="auto" classes="modal" @opened="renderIChecks()">
    <h4 class="modal-title">@lang('Create New Token')</h4>

    {!! Form::open(['route' => ['settings.create_token'], 'method' => 'POST']) !!}
    <div class="row">
        <div class="w-1/2">
            <div class="form-group">
                {{ Form::bsText('name', '', [], __('Name')) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="w-full">
            <label class="form-label">@lang('Permissions')</label>
                {{ Form::bsCheckbox('abilities[]', \Mss\Models\User::API_ABILITY_ARTICLE_GET, __('Get articles')) }}
                {{ Form::bsCheckbox('abilities[]', \Mss\Models\User::API_ABILITY_ARTICLE_EDIT, __('Edit articles')) }}
                {{ Form::bsCheckbox('abilities[]', \Mss\Models\User::API_ABILITY_ARTICLE_GROUP_GET, __('Get article groups')) }}
                {{ Form::bsCheckbox('abilities[]', \Mss\Models\User::API_ABILITY_ARTICLE_GROUP_EDIT, __('Edit article groups')) }}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" @click="$modal.hide('newTokenModal')">@lang('Cancel')</button>
        <button type="submit" class="btn btn-primary" id="saveNewToken">@lang('Save')</button>
    </div>
    {!! Form::close() !!}
</modal>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush