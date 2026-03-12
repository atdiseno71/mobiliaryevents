@extends('layouts.app')

@section('template_title')
    Crear Inventario
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                @include('layouts.mensaje-error')
                <div class="card card-default">
                    <div class="card-header d-flex justify-content-between">
                        <span>Nuevo registro de inventario</span>
                        <a class="btn btn-primary" href="{{ route('inventarios.index') }}">Atrás</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('inventarios.store') }}">
                            @csrf
                            @include('inventario.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
