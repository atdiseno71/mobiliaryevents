<div class="box box-info padding-1">
    <div class="box-body">

        {{-- Nombre --}}
        <div class="form-group">
            {{ Form::label('nombre') }}
            {{ Form::text('nombre', $data->nombre, ['class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''), 'placeholder' => 'Nombre', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('nombre', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- Grupo --}}
        <div class="form-group mb-3">
            {{ Form::label('grupo_id', 'Grupo') }}
            {{ Form::select('grupo_id', $grupos, $data->grupo_id, ['class' => 'form-select select2' . ($errors->has('grupo_id') ? ' is-invalid' : ''), 'placeholder' => 'Seleccione un grupo', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('grupo_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    @if (!$disabled)
        @include('layouts.components.form.submit-btn')
    @endif
</div>
