@extends('layouts.app')

@section('template_title')
    Cotizaciones
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="card_title">
                                {{ __('Administrar cotizaciones') }}
                            </span>

                            @can('cotizaciones.create')
                                <div>
                                    <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary btn-sm">
                                        {{ __('Nueva cotización') }}
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
                                        <th>Consecutivo</th>
                                        <th>Cliente</th>
                                        <th>Tipo Evento</th>
                                        <th>Ciudad</th>
                                        <th>Estado</th>
                                        <th>Total</th>
                                        <th>Creado Por</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No hay cotizaciones registradas.
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
            ajaxUrl: "{{ route('cotizaciones.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    width: '30px'
                },
                {
                    data: 'consecutivo',
                    name: 'consecutivo',
                    title: 'Consecutivo'
                },
                {
                    data: 'cliente',
                    name: 'cliente.nombre',
                    title: 'Cliente',
                    defaultContent: '-'
                },
                {
                    data: 'tipo_evento',
                    name: 'tipoEvento.nombre',
                    title: 'Tipo Evento',
                    defaultContent: '-'
                },
                {
                    data: 'ciudad.nombre',
                    name: 'ciudad.nombre',
                    title: 'Ciudad',
                    render: function(data) {
                        return data ?? '-';
                    }
                },
                {
                    data: 'estado',
                    name: 'estado.nombre',
                    title: 'Estado',
                    defaultContent: '-'
                },
                {
                    data: 'total_calculado',
                    name: 'total_calculado',
                    title: 'Total',
                    defaultContent: '-'
                },
                {
                    data: 'creador',
                    name: 'creador.name',
                    title: 'Creado por',
                    render: function(data, type, row) {
                        return row.creador && row.creador.name ? row.creador.name : '-';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Acciones',
                    orderable: false,
                    searchable: false
                }
            ]
        };
    </script>
@endsection
