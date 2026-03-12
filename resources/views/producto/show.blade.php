@extends('layouts.app3')

@section('title', 'Reporte de ventas')

@section('content')
    @include('template.cabezote')
    <h5 style="text-align: center"><strong>TABLA REPORTE PRODUCTOS A LA VENTA</strong></h5>
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th class="alineacion-left">No</th>
                <th class="alineacion-left">Nombre</th>
                <th class="alineacion-left">Categoria</th>
                <th class="alineacion-left">Opción</th>
                <th class="alineacion-left">Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pventas as $pventa)
                <tr class="alineacion-left">
                    <td>{{ $i++ }}</td>

                    <td>{{ $pventa->nombre }}</td>
                    <td>{{ $pventa->categoria->nombre }}</td>
                    <td>{{ $pventa->opcion > 0 ? 'Venta' : 'Compra' }}</td>
                    <td>{{ $pventa->stock }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h5 style="text-align: center"><strong>TABLA REPORTE PRODUCTOS PARA COMPRAR</strong></h5>
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th class="alineacion-left">No</th>
                <th class="alineacion-left">Nombre</th>
                <th class="alineacion-left">Categoria</th>
                <th class="alineacion-left">Opción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pcompras as $pcompra)
                <tr class="alineacion-left">
                    <td class="alineacion-left">{{ $i++ }}</td>

                    <td class="alineacion-left">{{ $pcompra->nombre }}</td>
                    <td class="alineacion-left">{{ $pcompra->categoria->nombre }}</td>
                    <td class="alineacion-left">{{ $pcompra->opcion > 0 ? 'Venta' : 'Compra' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
