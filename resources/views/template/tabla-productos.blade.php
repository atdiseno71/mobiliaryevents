<!--=====================================
LA TABLA DE PRODUCTOS
======================================-->

<div class="col-12 col-md-6">
    <div class="card card-default">
        <div class="card-body">
            <table class="table tablaVentas">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Descripcion</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $producto)
                        <tr>
                            <td>{{ $producto->id }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ $producto->stock != 0 ? $producto->stock  : 'N/A' }}</td>
                            <td>
                                <button type="button" class="btn btn-primary btnAgregarProducto recuperarBoton">Agregar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
