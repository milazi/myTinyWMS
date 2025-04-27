<div class="form-group">
    {!! Form::label('roles', __('Roles'), ['class' => 'control-label']) !!}
    {!! Form::select('roles[]', App\Models\Role::all()->pluck('display_name', 'id'), $values->pluck('id'), ['multiple' => 'multiple', 'placeholder' => __('Please choose'), 'class' => 'form-control']) !!}
</div>