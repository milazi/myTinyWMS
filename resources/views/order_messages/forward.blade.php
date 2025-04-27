@extends('layout.app')

@section('title', __('Forward Message'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Overview')</a>
    </li>
    <li class="active">
        <strong>@lang('Forward Message')</strong>
    </li>
@endsection

@section('summernote_show_signature_button', true)
@section('summernote_signature', html_entity_decode(Auth::user()->signature))

@section('content')
<div class="row">
    <div class="w-8/12 mr-4">
        {!! Form::open(['route' => ['order.message_forward', $message], 'method' => 'POST', 'id' => 'forwardMessageForm']) !!}
        <div class="card">
            <div class="card-header">
                <h5>@lang('Forward Message') </h5>
            </div>
            <div class="card-content">
                {{ Form::bsText('receiver', $preSetReceiver, [], __('Recipient (multiple separated by comma)')) }}
                {{ Form::bsText('subject', $preSetSubject, [], __('Subject')) }}

                @if (!empty($message->htmlBody))
                    {{ Form::wysiwygEditor('content', $message->htmlBody, [], __('Message')) }}
                @else
                    {{ Form::wysiwygEditor('content', nl2br($message->textBody), [], __('Message')) }}
                @endif

                <hr class="hr-line-solid">
                {!! Form::submit(__('Send'), ['class' => 'btn btn-primary', 'id' => 'send-message']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="w-4/12">
        <div class="card">
            <div class="card-header">
                <h5>@lang('Attachments')</h5>
            </div>
            <div class="card-content">
                @if($message->attachments->count())
                <ul>
                    @foreach($message->attachments as $attachment)
                    <li>{{ $attachment['orgFileName'] }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection