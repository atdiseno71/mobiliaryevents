@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- Formulario de actualización de Empresa --}}
            {!! Form::model($empresa, [
                'route' => ['empresa.update', $empresa->id],
                'method' => 'PUT',
                'files' => true,
            ]) !!}

            <div class="row">
                <div class="col-12 col-md-6">
                    {{-- NIT --}}
                    <div class="form-group">
                        {{ Form::label('nit', 'NIT') }}
                        {{ Form::text('nit', $empresa->nit, [
                            'class' => 'form-control' . ($errors->has('nit') ? ' is-invalid' : ''),
                            'placeholder' => '900.723.262-0',
                        ]) }}
                        {!! $errors->first('nit', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    {{-- Nombre --}}
                    <div class="form-group">
                        {{ Form::label('nombre', 'Nombre de la Empresa') }}
                        {{ Form::text('nombre', $empresa->nombre, [
                            'class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''),
                            'placeholder' => 'FIERRO PRODUCCIONES',
                        ]) }}
                        {!! $errors->first('nombre', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    {{-- Email --}}
                    <div class="form-group">
                        {{ Form::label('email', 'Correo electrónico') }}
                        {{ Form::email('email', $empresa->email, [
                            'class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''),
                            'placeholder' => 'empresa@correo.com',
                        ]) }}
                        {!! $errors->first('email', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    {{-- Página web --}}
                    <div class="form-group">
                        {{ Form::label('pagina_web', 'Página web') }}
                        {{ Form::text('pagina_web', $empresa->pagina_web, [
                            'class' => 'form-control' . ($errors->has('pagina_web') ? ' is-invalid' : ''),
                            'placeholder' => 'https://www.ejemplo.com',
                        ]) }}
                        {!! $errors->first('pagina_web', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    {{-- País --}}
                    <div class="form-group">
                        {{ Form::label('pais', 'País') }}
                        {{ Form::text('pais', $empresa->pais, [
                            'class' => 'form-control' . ($errors->has('pais') ? ' is-invalid' : ''),
                            'placeholder' => 'Colombia',
                        ]) }}
                        {!! $errors->first('pais', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    {{-- Región --}}
                    <div class="form-group">
                        {{ Form::label('region', 'Región / Departamento') }}
                        {{ Form::text('region', $empresa->region, [
                            'class' => 'form-control' . ($errors->has('region') ? ' is-invalid' : ''),
                            'placeholder' => 'Valle del Cauca',
                        ]) }}
                        {!! $errors->first('region', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    {{-- Ciudad --}}
                    <div class="form-group">
                        {{ Form::label('ciudad', 'Ciudad') }}
                        {{ Form::text('ciudad', $empresa->ciudad, [
                            'class' => 'form-control' . ($errors->has('ciudad') ? ' is-invalid' : ''),
                            'placeholder' => 'Palmira',
                        ]) }}
                        {!! $errors->first('ciudad', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    {{-- Dirección --}}
                    <div class="form-group">
                        {{ Form::label('direccion', 'Dirección') }}
                        {{ Form::text('direccion', $empresa->direccion, [
                            'class' => 'form-control' . ($errors->has('direccion') ? ' is-invalid' : ''),
                            'placeholder' => 'Calle 29 19-21',
                        ]) }}
                        {!! $errors->first('direccion', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    {{-- Teléfonos --}}
                    <div class="form-group">
                        {{ Form::label('telefonos', 'Teléfonos') }}
                        {{ Form::text('telefonos', $empresa->telefonos, [
                            'class' => 'form-control' . ($errors->has('telefonos') ? ' is-invalid' : ''),
                            'placeholder' => '315 5661002 - 317 4414930',
                        ]) }}
                        {!! $errors->first('telefonos', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    {{-- Logo --}}
                    <div class="form-group">
                        {{ Form::label('logo', 'Logo de la Empresa') }}
                        {{ Form::file('logo', [
                            'class' => 'form-control-file' . ($errors->has('logo') ? ' is-invalid' : ''),
                        ]) }}
                        {!! $errors->first('logo', '<div class="invalid-feedback">:message</div>') !!}

                        @if ($empresa->logo)
                            <div class="mt-3">
                                <img src="{{ asset("storage/{$empresa->logo}") }}" alt="Logo" width="120"
                                    class="rounded shadow-sm">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @include('layouts.components.form.submit-btn')

            {!! Form::close() !!}
        </div>
    </div>
@endsection
