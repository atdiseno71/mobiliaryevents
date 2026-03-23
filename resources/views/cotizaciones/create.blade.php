@extends('layouts.app')

@section('template_title')
    Crear cotización
@endsection

@section('css')
    <style>
        .product-row.hidden-row {
            display: none !important;
        }

        .product-group.hidden-group {
            display: none !important;
        }

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
                        <span class="card-title">Nueva cotización</span>
                        <span class="card_icon"></span>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('cotizaciones.index') }}">Atrás</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('cotizaciones.store') }}" role="form"
                            enctype="multipart/form-data">
                            @csrf

                            @include('cotizaciones.form')
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/views/cotizaciones.js') }}"></script>
@endsection
