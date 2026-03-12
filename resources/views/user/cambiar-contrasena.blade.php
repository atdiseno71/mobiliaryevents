@extends('layouts.app2')

@section('css')
    <style type="text/css">
        .login-page #back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: url('../img/back.png');
            background-size: cover;
            overflow: hidden;
            z-index: -1;
        }
    </style>
@endsection

@section('content')
    <div id="back"></div>
    <div class="login-box">
        @include('layouts.mensaje-error')
        <div class="card card-default">
            <div class="card-header">
                <span class="card-title">Cambiar contraseña</span>
                @if (!isset($message))
                    <span class="card_icon"></span>
                    <div class="float-right">
                        <a class="btn btn-danger" href="{{ route('usuarios.index') }}"> Cancelar</a>
                    </div>
                @endif
            </div>
            @if (isset($message))
                <div class="alert alert-warning">
                    <p>{{ $message }}</p>
                </div>
            @endif
            <div class="card-body">
                <form method="POST" action="{{ route('usuario.cambiar-contrasena', ['id' => $user->id]) }}" role="form"
                    enctype="multipart/form-data">
                    {{ method_field('PATCH') }}
                    @csrf
                    <input type="text" name="user" value="{{ $user->id }}" hidden>
                    <div class="form-group">
                        {{ Form::label('password', 'Nueva Contraseña') }}
                        {{ Form::password('password', ['placeholder' => '*********', 'class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('password', 'Confirmar Contraseña') }}
                        {{ Form::password('password_confirmation', ['placeholder' => '*********', 'class' => 'form-control']) }}
                    </div>
                    <div class="box-footer mt20">
                        <button type="submit" class="btn btn-primary btn-block">Cambiar contraseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
