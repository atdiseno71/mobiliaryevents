@extends('layouts.app3')

@section('title', 'Reporte de gastos')

@section('content')
    @include('template.cabezote')
    <h5 style="text-align: center"><strong>TABLA REPORTE DE GASTOS</strong></h5>
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Descripción</th>
                <th scope="col">Valor</th>
                <th scope="col">Fecha</th>
                <th scope="col">Tipo de gasto</th>
            </tr>
        </thead>
        <tbody style="text-align: right;">
        @foreach($gastos as $gasto)
            <tr>
                <th scope="row">{{ $gasto->id }}</th>
                <td>{{ $gasto->descripcion }}</td>
                <td>{{ $gasto->valor }}</td>
                <td>{{ date_create($gasto->fecha_gasto)->format('d-m-Y') }}</td>
                <td>{{ $gasto->tipoGasto->nombre }}</td>
                {{ $total = $total + $gasto->valor}}
            </tr>
        @endforeach
        </tbody>
    </table>
    <h3><Strong>TOTAL</Strong> {{ $total }}</h3>
@endsection
