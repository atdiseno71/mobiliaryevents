@extends('layouts.app')

@section('content')
    <section class="content container-fluid">
        <br>
        <div class="row">
            <div class="col-6">
                <span class="card-title">Actualizar compra</span>
            </div>
            <div class="col-6">
                <span class="card_icon"></span>
                <div class="float-right">
                    <a class="btn btn-primary" href="{{ route('compras.index') }}"> Atrás</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">

                @include('layouts.mensaje-error')

                <div class="card card-default">
                    <div class="card-body">
                        <form id="formCompra" method="POST" action="{{ route('compras.update', $compra->id) }}"
                            role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('compra.form')

                        </form>
                    </div>
                </div>
            </div>
            {{-- @include('template.tabla-productos') --}}
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/views/compras.js') }}"></script>
@endsection
