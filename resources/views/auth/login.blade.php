@extends('layouts.app2')

@section('title', 'Iniciar sesion')

@section('css')

    <style type="text/css">
        .login-page #back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: url('img/back.png');
            background-size: cover;
            overflow: hidden;
            z-index: -1;
        }
    </style>
@endsection

@section('content')
    <div id="back"></div>
    <div class="login-box">

        <div class="card">

            <div class="card-header text-center">
                <h3><strong>{{ $title }}</strong></h3>
            </div>

            <div class="card-body login-card-body">

                <form method="post" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label>Ingrese tu usuario</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" />
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Ingresa tu contraseña</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" />
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" id="button" name="button" class="btn btn-dark btn-block">
                            {{ 'Iniciar sesion' }}
                            <span class="fas fa-sign-in-alt"></span>
                        </button>
                    </div>
                </form>

            </div>

        </div>

    </div>

@endsection
