@extends('layouts.app')

@section('content')
    <section class="content container-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    @include('layouts.mensaje-error')
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Generar reporte de compra</span>
                            <span class="card_icon"></span>
                            <div class="float-right">
                                <a class="btn btn-primary" href="{{ route('compras.index') }}"> Atrás</a>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <p>Si no especifica la fecha, traerá el reporte del día actual</p>
                        </div>
                        <div class="card-body">
                            <form id="formReporte" method="POST" action="{{ route('compras.pdf') }}" role="form"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="box box-info padding-1">
                                    <div class="box-body">
                                        {{-- Fecha inicial --}}
                                        <div class="form-group">
                                            {{ Form::label('fechaInicial', 'Fecha inicial') }}
                                            {{ Form::date('fechaInicial', $fecha, ['class' => 'form-control' . ($errors->has('fechaInicial') ? ' is-invalid' : '')]) }}
                                            {!! $errors->first('fechaInicial', '<div class="invalid-feedback">:message</div>') !!}
                                        </div>

                                        {{-- Fecha final --}}
                                        <div class="form-group">
                                            {{ Form::label('fechaFinal', 'Fecha final') }}
                                            {{ Form::date('fechaFinal', $fecha, ['class' => 'form-control' . ($errors->has('fechaFinal') ? ' is-invalid' : '')]) }}
                                            {!! $errors->first('fechaFinal', '<div class="invalid-feedback">:message</div>') !!}
                                        </div>

                                        {{-- Tipo de reporte --}}
                                        <div class="form-group">
                                            {{ Form::label('tipo_reporte', 'Tipo de reporte') }}
                                            {{ Form::select(
                                                'tipo_reporte',
                                                [
                                                    'pdf' => 'PDF',
                                                    'excel' => 'Excel',
                                                ],
                                                null,
                                                [
                                                    'class' => 'form-control select2 tipo_reporte' . ($errors->has('tipo_reporte') ? ' is-invalid' : ''),
                                                    'placeholder' => 'Seleccione un tipo',
                                                ],
                                            ) }}
                                            {!! $errors->first('tipo_reporte', '<div class="invalid-feedback">:message</div>') !!}
                                        </div>

                                        <div class="box-footer mt20">
                                            <button type="submit" class="btn btn-outline-danger btn-block">Generar
                                                reporte</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
