@extends('layouts.app')

@section('template_title')
    Crear Combo
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                @include('layouts.mensaje-error')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Nuevo Combo</span>
                        <span class="card_icon"></span>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('combinaciones.index') }}"> Atrás</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('combinaciones.store') }}" role="form"
                            enctype="multipart/form-data">
                            @csrf

                            @include('combinaciones.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/views/combinaciones.js') }}"></script>
@endsection
