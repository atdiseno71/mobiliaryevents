<div class="card shadow-sm">
    <div class="card-body">

        {{-- ID oculto si estás editando --}}
        @if (isset($user->id))
            <input type="hidden" name="user" value="{{ $user->id }}">
        @endif

        <div class="row">
            <div class="col-12 col-md-6">
                {{-- Código --}}
                <div class="form-group">
                    {{ Form::label('codigo', 'Código') }}
                    {{ Form::text('codigo', $user->codigo, [
                        'class' => 'form-control' . ($errors->has('codigo') ? ' is-invalid' : ''),
                        'placeholder' => 'Código',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('codigo', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Tipo de identificación --}}
                <div class="form-group">
                    {{ Form::label('tipo_identificacion', 'Tipo de Identificación') }}
                    {{ Form::select(
                        'tipo_identificacion',
                        [
                            'CC' => 'Cédula de Ciudadanía',
                            'CE' => 'Cédula de Extranjería',
                            'NIT' => 'NIT',
                            'PAS' => 'Pasaporte',
                        ],
                        $user->tipo_identificacion,
                        [
                            'class' => 'form-control select2' . ($errors->has('tipo_identificacion') ? ' is-invalid' : ''),
                            'placeholder' => 'Seleccione...',
                            'disabled' => $disabled ? 'disabled' : null,
                        ],
                    ) }}
                    {!! $errors->first('tipo_identificacion', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Identificación --}}
                <div class="form-group">
                    {{ Form::label('identificacion', 'Número de Identificación') }}
                    {{ Form::text('identificacion', $user->identificacion, [
                        'class' => 'form-control' . ($errors->has('identificacion') ? ' is-invalid' : ''),
                        'placeholder' => 'Ingrese número de documento',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('identificacion', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Nombre completo --}}
                <div class="form-group">
                    {{ Form::label('name', 'Nombre completo') }}
                    {{ Form::text('name', $user->name, [
                        'class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''),
                        'placeholder' => 'Nombre completo',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Email --}}
                <div class="form-group">
                    {{ Form::label('email', 'Correo electrónico') }}
                    {{ Form::email('email', $user->email, [
                        'class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''),
                        'placeholder' => 'Correo electrónico',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('email', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- País --}}
                <div class="form-group">
                    {{ Form::label('pais_id', 'País') }}
                    {{ Form::select('pais_id', $paises, $user->pais_id, [
                        'class' => 'form-control select2' . ($errors->has('pais_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione un país',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('pais_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Departamento --}}
                <div class="form-group">
                    {{ Form::label('departamento_id', 'Departamento / Región') }}
                    {{ Form::select('departamento_id', $departamentos, $user->departamento_id, [
                        'class' => 'form-control select2' . ($errors->has('departamento_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione un departamento',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('departamento_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Ciudad --}}
                <div class="form-group">
                    {{ Form::label('ciudad_id', 'Ciudad') }}
                    {{ Form::select('ciudad_id', $ciudades, $user->ciudad_id, [
                        'class' => 'form-control select2' . ($errors->has('ciudad_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione una ciudad',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('ciudad_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Dirección --}}
                <div class="form-group">
                    {{ Form::label('direccion', 'Dirección') }}
                    {{ Form::text('direccion', $user->direccion, [
                        'class' => 'form-control' . ($errors->has('direccion') ? ' is-invalid' : ''),
                        'placeholder' => 'Dirección',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('direccion', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Teléfono fijo --}}
                <div class="form-group">
                    {{ Form::label('telefono_fijo', 'Teléfono fijo') }}
                    {{ Form::text('telefono_fijo', $user->telefono_fijo, [
                        'class' => 'form-control' . ($errors->has('telefono_fijo') ? ' is-invalid' : ''),
                        'placeholder' => 'Ej: 1234567',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('telefono_fijo', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Teléfono móvil --}}
                <div class="form-group">
                    {{ Form::label('telefono_movil', 'Teléfono móvil') }}
                    {{ Form::text('telefono_movil', $user->telefono_movil, [
                        'class' => 'form-control' . ($errors->has('telefono_movil') ? ' is-invalid' : ''),
                        'placeholder' => 'Ej: 3001234567',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('telefono_movil', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Nivel --}}
                <div class="form-group">
                    {{ Form::label('nivel', 'Nivel de usuario') }}
                    {{ Form::select('nivel', $roles, $user->nivel_original, [
                        'class' => 'form-control select2' . ($errors->has('nivel') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione un nivel',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('nivel', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Estado --}}
                <div class="form-group">
                    {{ Form::label('estado', 'Estado') }}
                    {{ Form::select('estado', ['Activo' => 'Activo', 'Inactivo' => 'Inactivo'], $user->estado, [
                        'class' => 'form-control select2' . ($errors->has('estado') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione el estado',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('estado', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            @if (!isset($user->id))
                <div class="col-12 col-md-6">
                    {{-- Contraseña --}}
                    <div class="form-group">
                        {{ Form::label('password', 'Contraseña') }}

                        <div class="input-group">
                            {{ Form::password('password', [
                                'class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''),
                                'placeholder' => 'Contraseña',
                                'id' => 'password',
                                'disabled' => $disabled ? 'disabled' : null,
                            ]) }}

                            <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePassword('password', this)">
                                <i class="fa fa-eye"></i>
                            </button>

                            {!! $errors->first('password', '<div class="invalid-feedback d-block">:message</div>') !!}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    {{-- Confirmar contraseña --}}
                    <div class="form-group">
                        {{ Form::label('password_confirmation', 'Confirmar contraseña') }}

                        <div class="input-group">
                            {{ Form::password('password_confirmation', [
                                'class' => 'form-control' . ($errors->has('password_confirmation') ? ' is-invalid' : ''),
                                'placeholder' => 'Repite la contraseña',
                                'id' => 'password_confirmation',
                                'disabled' => $disabled ? 'disabled' : null,
                            ]) }}

                            <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePassword('password_confirmation', this)">
                                <i class="fa fa-eye"></i>
                            </button>

                            {!! $errors->first('password_confirmation', '<div class="invalid-feedback d-block">:message</div>') !!}
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-12">
                {{-- Foto --}}
                <div class="form-group">
                    {{ Form::label('foto', 'Foto de perfil') }}
                    {{ Form::file('foto', [
                        'class' => 'form-control-file' . ($errors->has('foto') ? ' is-invalid' : ''),
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('foto', '<div class="invalid-feedback">:message</div>') !!}

                    @if ($user->foto)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto de {{ $user->name }}"
                                width="100" class="rounded shadow-sm">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (!$disabled)
        @include('layouts.components.form.submit-btn')
    @endif
</div>
