<table border="1" style="border-collapse: collapse; width: 100%; text-align: center;">
    <thead style="background-color: #f2f2f2;">
        <tr>
            <th>No</th>
            <th>Código</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Tipo</th>
            <th>Cant. Arrobas</th>
            <th>Cant. Kilos</th>
            <th>VR ARRO</th>
            <th>Neto Pagado</th>
            <th>Fecha</th>
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

                $filtered = array_values(array_filter($productos, function ($a) use ($listaProducto) {
                    return $a['descripcion'] == ($listaProducto['combi_nombre'] ?? '');
                }));

                $producto = $filtered[0] ?? null;

                $productoArrobas = 0;
                $productoArrobas2 = 0;
                $productoKilos   = 0;

                if ($producto) {
                    if (in_array($listaCompra['tipo_cantidad'], ['1', 1])) {
                        // Arrobas
                        $productoArrobas += $producto['cantidad'];
                        $productoKilos = $productoArrobas * $listaProducto['peso'];
                        $productoArrobas2 = $productoArrobas;
                    } else {
                        // Kilos
                        $productoKilos = $producto['cantidad'];
                        $productoArrobas = 0;
                        $productoArrobas2 = $productoKilos / ($listaProducto['peso'] ?? 1);
                    }
                }

                $totalArrobas += $productoArrobas2;
                $totalKilos   += $productoKilos;
                $totalPagado  += $listaCompra['total'];
            @endphp

            <tr>
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
                <td>{{ number_format($producto['precio'] ?? 0, 0) }}</td>
                <td>{{ number_format($listaCompra['total'], 0) }}</td>
                <td>{{ date_create($listaCompra['fecha_venta'])->format('d-m-Y') }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot style="background-color: #f9f9f9; font-weight: bold;">
        <tr>
            <td colspan="5" style="text-align: right;">TOTALES</td>
            <td>{{ number_format($totalArrobas, 2) }}</td>
            <td>{{ number_format($totalKilos, 2) }}</td>
            <td></td>
            <td>{{ number_format($totalPagado, 0) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
