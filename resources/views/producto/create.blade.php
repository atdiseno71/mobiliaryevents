@extends('layouts.app')

@section('template_title')
    Create Equipo-Insumo
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                @include('layouts.mensaje-error')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Nuevo Producto</span>
                        <span class="card_icon"></span>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('productos.index') }}"> Atrás</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('productos.store') }}" role="form"
                            enctype="multipart/form-data">
                            @csrf

                            @include('producto.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/views/productos.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chkSerie = document.getElementById('inventario_por_serie');
            const subref = document.getElementById('subreferencia_wrap');

            function toggleSubref() {
                if (chkSerie.checked) {
                    subref.style.display = 'block';
                } else {
                    subref.style.display = 'none';
                }
            }

            // Ejecutar en carga inicial (por si viene marcado desde BD)
            toggleSubref();

            // Ejecutar cuando el usuario cambie el switch
            chkSerie.addEventListener('change', toggleSubref);
        });
    </script>
@endsection
