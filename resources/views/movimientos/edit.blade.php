@extends('layouts.app')

@section('template_title')
    Actualizar movimiento
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
        <div class="">
            <div class="col-md-12">

                @include('layouts.mensaje-error')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Actualizar movimiento</span>
                        <span class="card_icon"></span>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('movimientos.index') }}"> Atrás</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('movimientos.update', $data->id) }}" role="form"
                            enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
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
    <script>
        window.preloadedProductos = @json($preloadedProductos);
        window.detallesRemision = @json($data->remision->detalles);
    </script>
    <script src="{{ asset('js/views/movimientos_editar.js') }}"></script>
@endsection
