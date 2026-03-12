<div class="box box-info padding-1">
    <div class="box-body">

        <div class="row mb-4">
            <div class="col-12 col-md-6">
                {{-- Activo --}}
                <label class="switch">
                    {{ Form::checkbox('activo', 1, $data->activo, [
                        'id' => 'activo',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    <span class="slider"></span>
                </label>
                <label for="activo" class="ml-2">¿Activo?</label>
            </div>
        </div>

        <div class="row">

            <div class="col-12 col-md-6">
                {{-- Nombre --}}
                <div class="form-group mb-3">
                    {{ Form::label('nombre', 'Nombre *') }}
                    {{ Form::text('nombre', $data->nombre, [
                        'class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''),
                        'placeholder' => 'Nombre del almacén',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('nombre', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Ciudad --}}
                <div class="form-group mb-3">
                    {{ Form::label('ciudad_id', 'Ciudad') }}
                    {{ Form::select('ciudad_id', $ciudades, $data->ciudad_id, [
                        'class' => 'form-select select2' . ($errors->has('ciudad_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione una ciudad',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('ciudad_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Responsable --}}
                <div class="form-group mb-3">
                    {{ Form::label('responsable_id', 'Responsable') }}
                    {{ Form::select('responsable_id', $responsables, $data->responsable_id, [
                        'class' => 'form-select select2' . ($errors->has('responsable_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione un usuario',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('responsable_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Dirección --}}
                <div class="form-group mb-3">
                    {{ Form::label('direccion', 'Dirección') }}
                    {{ Form::text('direccion', $data->direccion, [
                        'class' => 'form-control' . ($errors->has('direccion') ? ' is-invalid' : ''),
                        'placeholder' => 'Dirección del almacén',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('direccion', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Teléfono --}}
                <div class="form-group mb-3">
                    {{ Form::label('telefono', 'Teléfono') }}
                    {{ Form::text('telefono', $data->telefono, [
                        'class' => 'form-control' . ($errors->has('telefono') ? ' is-invalid' : ''),
                        'placeholder' => 'Número de teléfono',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('telefono', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Latitud --}}
                <div class="form-group mb-3">
                    {{ Form::label('latitud', 'Latitud') }}
                    {{ Form::number('latitud', $data->latitud, [
                        'step' => '0.0000001',
                        'class' => 'form-control' . ($errors->has('latitud') ? ' is-invalid' : ''),
                        'placeholder' => 'Valor de latitud',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('latitud', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Longitud --}}
                <div class="form-group mb-3">
                    {{ Form::label('longitud', 'Longitud') }}
                    {{ Form::number('longitud', $data->longitud, [
                        'step' => '0.0000001',
                        'class' => 'form-control' . ($errors->has('longitud') ? ' is-invalid' : ''),
                        'placeholder' => 'Valor de longitud',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('longitud', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12">
                {{-- Descripción --}}
                <div class="form-group mb-3">
                    {{ Form::label('descripcion', 'Descripción') }}
                    {{ Form::textarea('descripcion', $data->descripcion, [
                        'class' => 'form-control' . ($errors->has('descripcion') ? ' is-invalid' : ''),
                        'rows' => 3,
                        'placeholder' => 'Observaciones o notas sobre el almacén',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('descripcion', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

        </div>

    </div>

    @if (!$disabled)
        @include('layouts.components.form.submit-btn')
    @endif
</div>
