@extends('layouts.app')

@section('template_title')
    Create Venta
@endsection

@section('content')
    <section class="content container-fluid">
        <br>
        <div class="row">
            <div class="col-6">
                <span class="card-title">Nueva venta</span>
            </div>
            <div class="col-6">
                <span class="card_icon"></span>
                <div class="float-right">
                    <a class="btn btn-primary" href="{{ route('ventas.index') }}"> Atrás</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">

                @include('layouts.mensaje-error')

                <div class="card card-default">
                    <div class="card-body">
                        <form id="formVenta" method="POST" action="{{ route('ventas.store') }}" role="form"
                            enctype="multipart/form-data">
                            @csrf

                            @include('venta.form')

                        </form>
                    </div>
                </div>
            </div>
            {{-- @include('template.tabla-productos') --}}
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/views/ventas.js') }}"></script>
@endsection
