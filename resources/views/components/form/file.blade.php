<div class="fileinput fileinput-new input-group" data-provides="fileinput">
    <div class="form-control" data-trigger="fileinput">
        <i class="glyphicon glyphicon-file fileinput-exists"></i>
        <span class="fileinput-filename"></span>
    </div>
    <span class="input-group-addon btn btn-default btn-file">
        <span class="fileinput-new">@lang('choose')</span>
        <span class="fileinput-exists">@lang('change')</span>
        {!! Form::file($name, $attributes) !!}
    </span>
    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">@lang('delete')</a>
</div>
@if(!empty($helpBlock))
    <p class="help-block">{{ $helpBlock }}</p>
@endif