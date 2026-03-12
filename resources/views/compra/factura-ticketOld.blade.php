@extends('layouts.app4')

@section('title', 'Factura compra')

@section('content')
    <div class="ticket centrado">
        @include('template.cabezote-factura')
        <table>
            <tbody>
                <tr>
                    <th class="cantidad">{{ $compra->tipo_cantidad != '1' ? 'kg' : '@' }}</th>
                    <th class="producto">Lb</th>
                </tr>
                <?php
                $total = 0;
                $cantidadProducto = 0;
                $listaProducto = json_decode($compra->productos, true);
                foreach ($listaProducto as $producto) {
                    if($producto["tipo_cantidad"] != 1){
                        $cantidadProducto += $producto["cantidad"];
                    }else {
                        $cantidadProducto = $producto["cantidad"] * $producto["peso"];
                    }
                    $total += $cantidadProducto * $producto["precio"];
                ?>
                    <tr>
                        <th class="cantidad">{{ number_format($producto["cantidad"], 0) }}</th>
                        <th class="producto">{{ $producto["libras"] }}</th>
                    </tr>
                    <tr>
                        <th class="cantidad">PRECIO</th>
                        <th class="producto">{{ number_format($producto["precio"], 0) }}</th>
                    </tr>
                    <tr>
                        <th class="cantidad">PRODUCTO&nbsp;</th>
                        <th class="producto">{{ $producto["descripcion"] }}</th>
                    </tr>
                <?php } ?>
            </tbody>
            <tr>
                <td class="producto">
                    <strong>TOTAL</strong>
                </td>
                <td class="precio">
                    $<?php echo number_format($compra->total, 0) ?>
                </td>
            </tr>
        </table>
        <p class="centrado">¡GRACIAS POR SU VENTA!</p>
    </div>
@endsection
