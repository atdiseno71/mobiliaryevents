@extends('layouts.app')

@section('template_title')
    Crear Rol
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                @include('layouts.mensaje-error')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Nuevo rol</span>
                        <span class="card_icon"></span>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('roles.index') }}"> Atrás</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('roles.store') }}" role="form"
                            enctype="multipart/form-data">
                            @csrf

                            @include('roles.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
