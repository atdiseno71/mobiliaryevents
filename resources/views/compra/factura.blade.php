@extends('layouts.app4')

@section('title', 'Factura compra')

@section('content')
    <div class="ticket">

        {{-- CABEZOTE --}}
        <table class="cabezote-tabla">
            <tr>
                <!-- Empresa -->
                <td class="cabezote-empresa border-bottom">
                    <p><strong>GRANO DE ORO</strong></p>
                    <p>Tel: 317 251 8270</p>
                    <p>Cra. 5 Calle 5 Frente al parque</p>
                    <p>Ceilán, Valle</p>
                </td>

                <!-- Documento -->
                <td class="cabezote-doc border-bottom">
                    <p><strong>Factura de Compra No: <br> {{ $codigo }}</strong></p>
                </td>
            </tr>
            <tr>
                <!-- Cliente -->
                <td class="cabezote-empresa">
                    <p>{{ $compra->cliente->nombre ?? '---' }}</p>
                    <p>CC: {{ $compra->cliente->cedula ?? '---' }}</p>
                    <p>{{ $compra->cliente->direccion ?? '' }}</p>
                    <p>{{ $compra->cliente->telefono ?? '' }}</p>
                </td>

                <!-- Factura datos -->
                <td class="cabezote-doc2">
                    <p><strong>Fecha Factura:</strong>
                        {{ \Carbon\Carbon::parse($compra->fecha_venta)->format('d/m/Y h:i a') }}</p>
                    <p><strong>Fecha Vencimiento:</strong>
                        {{ \Carbon\Carbon::parse($compra->fecha_venta)->format('d/m/Y h:i a') }}</p>
                    <p><strong>Consecutivo:</strong> {{ $compra->codigo_factura }}</p>
                </td>
            </tr>
        </table>
        <hr style="border: 0; border-top: 1px solid #000; margin: 5px 0; width: 100%;">
        {{-- TABLA DETALLE --}}
        <table class="tabla-detalle">
            <thead>
                <tr class="border-bottom">
                    <th>DESCRIPCIÓN</th>
                    <th>UNID</th>
                    <th>IVA</th>
                    <th>CANT</th>
                    <th>Vr. ARRO</th>
                    <th>Vr. TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $listaProducto = json_decode($compra->productos, true);
                    $tipoUnidad = $compra->tipo_cantidad == 1 ? 'ARRO' : 'KG'; // 1: unidad, 2: peso
                @endphp

                @foreach ($listaProducto as $producto)
                    @php
                        /* $cantidad = $producto['tipo_cantidad'] != 1
                        ? $producto['cantidad']
                        : $producto['cantidad'] * $producto['peso']; */

                        $cantidad = $producto['cantidad'];

                        $subtotal = $cantidad * $producto['precio'];
                        $total += $subtotal;
                    @endphp

                    <tr class="border-bottom">
                        <td class="producto">{{ $producto['descripcion'] }}</td>
                        <td class="cantidad">{{ $tipoUnidad }}</td>
                        <td class="cantidad">0</td>
                        <td class="cantidad">{{ number_format($cantidad, 2) }}</td>
                        <td class="precio">{{ number_format($producto['precio'], 0) }}</td>
                        <td class="precio">{{ number_format($subtotal, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-bottom">
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td><strong>SUBTOTAL</strong></td>
                    <td colspan="3" class="precio">${{ number_format($total, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td><strong>IVA</strong></td>
                    <td colspan="3" class="precio">$0</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td><strong>NETO A PAGAR</strong></td>
                    <td colspan="3" class="precio"><strong>${{ number_format($compra->total, 0) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
