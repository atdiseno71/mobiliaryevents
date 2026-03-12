@extends('layouts.app')

@section('template_title')
    Movimientos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">

                            <span id="card_title">
                                {{ __('Administrar movimientos') }}
                            </span>

                            @can('movimientos.create')
                                <div>
                                    <a href="{{ route('movimientos.create') }}" class="btn btn-primary btn-sm">
                                        {{ __('Nuevo movimiento') }}
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>

                    @include('layouts.mensaje-error')

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Almacén</th>
                                        <th>Remisión</th>
                                        <th>Tipo</th>
                                        <th>Observaciones</th>
                                        <th>Creado Por</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No hay remisiones registradas.
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src="{{ asset('js/plugins/sweetalert.js') }}"></script>

    <script>
        window.defaultPageLength = {{ $perPage }};
        window.tableConfigs = window.tableConfigs || {};

        window.tableConfigs['.table'] = {
            ajaxUrl: "{{ route('movimientos.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    title: 'ID'
                },
                {
                    data: 'almacen',
                    name: null,
                    title: 'Almacén'
                },
                {
                    data: 'remision',
                    name: null,
                    title: 'Remisión'
                },
                {
                    data: 'tipo',
                    name: null,
                    title: 'Tipo'
                },
                {
                    data: 'motivo',
                    name: null,
                    title: 'Observaciones'
                },
                {
                    data: 'creado_por',
                    name: null,
                    title: 'Creado Por'
                },
                {
                    data: 'estado',
                    name: null,
                    title: 'Estado'
                },
                {
                    data: 'action',
                    name: null,
                    title: 'Acciones',
                    orderable: false,
                    searchable: false
                }
            ]
        };
    </script>
@endsection
