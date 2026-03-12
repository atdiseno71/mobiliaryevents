@extends('layouts.app3')

@section('title', 'Reporte de ventas')

@section('content')
    @include('template.cabezote')
    <h5 style="text-align: center"><strong>TABLA REPORTE DE VENTA</strong></h5>
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th class="alineacion-left">No</th>
                <th class="alineacion-left">Código</th>
                <th class="alineacion-left">Cliente</th>
                <th class="alineacion-left">Vendedor</th>
                <th class="alineacion-left">Cantidad</th>
                <th class="alineacion-left">Total</th>
                <th class="alineacion-left">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
                <tr class="alineacion-left">
                    <td>{{ $i++ }}</td>

                    <td>{{ $venta->codigo_factura }}</td>
                    <td>{{ $venta->cliente->nombre }}</td>
                    <td>{{ $venta->user->name }}</td>
                    @php
                        $productos = json_decode($venta->productos, true);
                        $productoCantidad = 0;
                        foreach ($productos as $producto) {
                            $productoCantidad += $producto["cantidad"];
                        }
                        echo "<td>$productoCantidad</td>";
                        $cantidad += $productoCantidad;
                    @endphp
                    <td>{{ $venta->total }}</td>
                    <td>{{ date_create($venta->fecha_venta)->format('d-m-Y') }}</td>
                </tr>
                {{ $total = $total + $venta->total}}
            @endforeach
        </tbody>
    </table>
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th class="alineacion">CANTIDAD TOTAL</th>
                <th></th>
                <th></th>
                <th></th>
                <th class="alineacion">TOTAL</th>
            </tr>
        </thead>
        <tbody class="alineacion">
            <tr>
                <td class="alineacion">{{ number_format($cantidad, 2) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="alineacion">{{ number_format($total, 2) }}</td>

            </tr>
        </tbody>
    </table>
    </h3>
@endsection

