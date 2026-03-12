@extends('layouts.app4')

@section('title', 'Factura venta')

@section('content')
    <div class="ticket centrado">
        @include('template.cabezote-factura')
        <table>
            <tbody>
                <?php
                $total = 0;
                $cantidadProducto = 0;
                $listaProducto = json_decode($venta->productos, true);
                foreach ($listaProducto as $producto) {
                    $total += $producto["cantidad"] * $producto["precio"];
                ?>
                    <tr>
                        <th class="cantidad">PRODUCTO</th>
                        <th class="producto">{{ $producto["descripcion"] }}</th>
                    </tr>
                    <tr>
                        <th class="cantidad">CANT</th>
                        <th class="producto">{{ number_format($producto["cantidad"], 2) }}</th>
                    </tr>
                    <tr>
                        <th class="cantidad">PRECIO</th>
                        <th class="producto">{{ number_format($producto["precio"], 2) }}</th>
                    </tr>
                <?php } ?>
            </tbody>
            <tr>
                <td class="producto">
                    <strong>TOTAL</strong>
                </td>
                <td class="precio">
                    $<?php echo number_format($venta->total, 2) ?>
                </td>
            </tr>
        </table>
        <p class="centrado">¡GRACIAS POR SU VENTA!</p>
    </div>
@endsection
