@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ __('Administrar inventario') }}</span>
                            <div>
                                @can('inventarios.create')
                                    <a href="{{ route('inventarios.create') }}" class="btn btn-primary btn-sm">Nuevo registro</a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    @include('layouts.mensaje-error')

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover w-100" id="inventarioTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Producto</th>
                                        <th>QR</th>
                                        <th>Almacén</th>
                                        <th>Stock</th>
                                        <th>Activo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal incluido desde vista separada --}}
    @include('inventario.modal-mover-stock')
@endsection

@section('js')
    <script src="{{ asset('js/plugins/sweetalert.js') }}"></script>
    <script>
        window.defaultPageLength = {{ $perPage }};
        window.tableConfigs = window.tableConfigs || {};

        // Configuración de DataTable
        window.tableConfigs['.table'] = {
            ajaxUrl: "{{ route('inventarios.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    width: '20px',
                    title: 'No'
                },
                {
                    data: 'producto',
                    name: 'producto.nombre',
                    title: 'Producto'
                },
                {
                    data: 'codigo_qr',
                    name: 'producto.codigo_qr',
                    title: 'QR'
                },
                {
                    data: 'almacen',
                    name: 'almacen.nombre',
                    title: 'Almacén'
                },
                {
                    data: 'stock',
                    name: 'stock',
                    title: 'Stock'
                },
                {
                    data: 'activo',
                    name: 'activo',
                    title: 'Activo',
                    render: function(d) {
                        return d ? '<span class="badge bg-success">Sí</span>' :
                            '<span class="badge bg-secondary">No</span>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Acciones',
                    searchable: false,
                    orderable: false
                }
            ]
        };

        $(document).ready(function() {
            // Delegación de evento para abrir modal desde botones generados dinámicamente
            $('.table').on('click', 'button[data-toggle="modal"]', function() {
                var productoId = $(this).data('producto');
                var almacenId = $(this).data('almacen');

                $('#modalProductoId').val(productoId);
                $('#modalAlmacenId').val(almacenId);
                $('#tipo').val('');
                $('#cantidad').val('');
                $('#motivo').val('');

                $('#moverStockModal').modal('show');
            });

            // Reset formulario al cerrar modal
            $('#moverStockModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });
        });
    </script>
@endsection
