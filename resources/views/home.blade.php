@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/reloj.css') }}">
@endsection

@section('content')
<div class="wrap">
    <div class="widget">
        <div class="fecha">
            <p id="diaSemana" class="diaSemana"></p>
            <p id="dia" class="dia"></p>
            <p>de</p>
            <p id="mes" class="mes"></p>
            <p>del</p>
            <p id="year" class="year"></p>
        </div>
        <div class="reloj">
            <p id="horas" class="horas"></p>
            <p>:</p>
            <p id="minutos" class="minutos"></p>
            <p>:</p>
            <div class="caja-segundos">
                <p id="ampm" class="ampm"></p>
                <p id="segundos" class="segundos"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/reloj.js') }}"></script>
@endsection
