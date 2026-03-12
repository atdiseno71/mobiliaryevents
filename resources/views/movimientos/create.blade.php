@extends('layouts.app')

@section('template_title')
    Crear Movimiento
@endsection

@section('css')
    <style>
        li.product-row.product-movimiento-hidden {
            display: none !important;
        }
    </style>
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                @include('layouts.mensaje-error')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Nuevo movimiento</span>
                        <span class="card_icon"></span>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('movimientos.index') }}"> Atrás</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('movimientos.store') }}" role="form"
                            enctype="multipart/form-data">
                            @csrf

                            @include('movimientos.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/views/movimientos.js') }}"></script>
@endsection
