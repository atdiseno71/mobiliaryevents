@extends('layouts.app')

@section('template_title')
    Create Remision
@endsection

@section('css')
    <style>
        /* Oculta filas individuales dentro de cada grupo */
        .product-row.hidden-row {
            display: none !important;
        }

        /* Oculta el grupo entero cuando no tiene hijos visibles */
        .product-group.hidden-group {
            display: none !important;
        }

        /* que se note cuando está filtrado */

        .product-row {
            transition: opacity .15s ease, transform .15s ease;
        }

        .product-row.hidden-row {
            opacity: 0;
            transform: translateY(-4px);
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
                        <span class="card-title">Nueva remision</span>
                        <span class="card_icon"></span>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('remisiones.index') }}"> Atrás</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('remisiones.store') }}" role="form"
                            enctype="multipart/form-data">
                            @csrf

                            @include('remisiones.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/views/remisiones.js') }}"></script>
@endsection
