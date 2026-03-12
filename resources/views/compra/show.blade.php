@extends('layouts.app3')

@section('title', 'Reporte de compras')

@section('content')
    @include('template.cabezote')

    @foreach ($listaProductos as $listaProducto)
        <h5 style="text-align: center; text-transform: uppercase;">
            <strong>REPORTE DE COMPRA {{ $listaProducto['combi_nombre'] }}</strong>
        </h5>

        <table class="table">
            <thead class="table-dark">
                <tr>
                    <th class="alineacion-left">No</th>
                    <th class="alineacion-left">Código</th>
                    <th class="alineacion-left">Cliente</th>
                    <th class="alineacion-left">Vendedor</th>
                    <th class="alineacion-left">Tipo</th>
                    <th class="alineacion-left">Cant arro</th>
                    <th class="alineacion-left">Cant kg</th>
                    <th class="alineacion-left">Total</th>
                    <th class="alineacion-left">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $i = 1; 
                    $totalArrobas = 0;
                    $totalKilos   = 0;
                    $totalPagado  = 0;
                @endphp

                @foreach ($listaProducto['productos'] as $listaCompra)
                    @php
                        $productos = json_decode($listaCompra['productos'], true);

                        // Buscar el producto coincidente
                        $filtered = array_values(array_filter($productos, function($a) use($listaProducto) {
                            return $a['descripcion'] == ($listaProducto['combi_nombre'] ?? '');
                        }));

                        $producto = $filtered[0] ?? null;
                        $productoArrobas = 0;
                        $productoArrobas2 = 0;
                        $productoKilos   = 0;

                        if ($producto) {
                            // tipo_cantidad = 1 → arrobas, 0 → kilos
                            if (in_array($listaCompra['tipo_cantidad'], ['1', 1])) {
                                // Calcular en arrobas
                                $productoArrobas += $producto["cantidad"];
                                // kilos = arrobas * peso definido en el producto
                                $productoKilos = $productoArrobas * $listaProducto['peso'];
                                $productoArrobas2 = $productoArrobas;
                            } else {
                                // Si ya viene en kilos, no multiplicar
                                $productoKilos = $producto['cantidad'];
                                $productoArrobas = 0;
                                $productoArrobas2 = $productoKilos / ($listaProducto['peso'] ?? 1);
                            }
                        }

                        // acumular totales
                        $totalArrobas += $productoArrobas2;
                        $totalKilos   += $productoKilos;
                        $totalPagado  += $listaCompra['total'];
                    @endphp

                    <tr class="alineacion-left">
                        <td>{{ $i++ }}</td>
                        <td>{{ $listaCompra['codigo_factura'] }}</td>
                        <td>{{ $listaCompra['cliente'] }}</td>
                        <td>{{ $listaCompra['comprador'] }}</td>
                        <td>{{ in_array($listaCompra['tipo_cantidad'], ['0', 0]) ? 'Kg' : 'Arros' }}</td>
                        <td>
                            {{-- {{ in_array($listaCompra['tipo_cantidad'], ['1', 1]) ? number_format($productoArrobas, 2) : '—' }} --}}
                            {{ number_format($productoArrobas2, 2) }}
                        </td>
                        <td>{{ number_format($productoKilos, 2) }}</td>
                        <td>{{ number_format($listaCompra['total'], 0) }}</td>
                        <td>{{ date_create($listaCompra['fecha_venta'])->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totales --}}
        <table class="table">
            <thead class="table-dark">
                <tr>
                    <th class="alineacion">TOTAL ARROBAS</th>
                    <th class="alineacion">TOTAL KILOS</th>
                    <th class="alineacion">TOTAL PRECIO</th>
                </tr>
            </thead>
            <tbody class="alineacion">
                <tr>
                    <td>{{ number_format($totalArrobas, 2) }}</td>
                    <td>{{ number_format($totalKilos, 2) }}</td>
                    <td>{{ number_format($totalPagado, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach
@endsection
