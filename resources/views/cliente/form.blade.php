<div class="box box-info padding-1">
    <div class="box-body">

        {{-- Nombre --}}
        <div class="form-group mb-3">
            {{ Form::label('nombre') }}
            {{ Form::text('nombre', $cliente->nombre, ['class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''), 'placeholder' => 'Nombre', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('nombre', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- País --}}
        <div class="form-group mb-3">
            {{ Form::label('pais_id', 'País') }}
            {{ Form::select('pais_id', $paises, $cliente->pais_id, ['class' => 'form-select select2' . ($errors->has('pais_id') ? ' is-invalid' : ''), 'placeholder' => 'Seleccione un país', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('pais_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- Departamento --}}
        <div class="form-group mb-3">
            {{ Form::label('departamento_id', 'Región') }}
            {{ Form::select('departamento_id', $departamentos, $cliente->departamento_id, ['class' => 'form-select select2' . ($errors->has('departamento_id') ? ' is-invalid' : ''), 'placeholder' => 'Seleccione un departamento', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('departamento_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- Ciudad --}}
        <div class="form-group mb-3">
            {{ Form::label('ciudad_id', 'Ciudad') }}
            {{ Form::select('ciudad_id', $ciudades, $cliente->ciudad_id, ['class' => 'form-select select2' . ($errors->has('ciudad_id') ? ' is-invalid' : ''), 'placeholder' => 'Seleccione una ciudad', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('ciudad_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- Dirección --}}
        <div class="form-group mb-3">
            {{ Form::label('direccion', 'Dirección') }}
            {{ Form::text('direccion', $cliente->direccion, ['class' => 'form-control' . ($errors->has('direccion') ? ' is-invalid' : ''), 'placeholder' => 'Dirección de entrega', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('direccion', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- Teléfono --}}
        <div class="form-group mb-3">
            {{ Form::label('telefonos', 'Teléfono') }}
            {{ Form::tel('telefonos', $cliente->telefonos, ['class' => 'form-control phone' . ($errors->has('telefonos') ? ' is-invalid' : ''), 'placeholder' => 'Teléfono', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('telefonos', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- Email --}}
        <div class="form-group mb-3">
            {{ Form::label('email', 'Correo electrónico') }}
            {{ Form::email('email', $cliente->email, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => 'Correo electrónico', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('email', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    @if (!$disabled)
        @include('layouts.components.form.submit-btn')
    @endif
</div>
