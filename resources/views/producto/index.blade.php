@extends('layouts.app')

@section('template_title')
    Equipos-Insumos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Administrar Equipos-Insumos') }}
                            </span>

                            @can('productos.create')
                                <span class="card_icon"></span>
                                <div class="float-right">
                                    <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm">
                                        {{ __('Nuevo Equipo-Insumo') }}
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
                                        <th>Nombre</th>
                                        <th>Marca</th>
                                        <th>Código QR</th>
                                        <th>Grupo</th>
                                        <th>Clase</th>
                                        @can('productos.ver_valor_compra')
                                            <th>Valor Compra</th>
                                        @endcan

                                        @can('productos.ver_valor_alquiler')
                                            <th>Valor Alquiler</th>
                                        @endcan
                                        <th>Activo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            No hay Equipos-Insumos registrados.
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
            ajaxUrl: "{{ route('productos.index') }}",
            columns: [{
                    data: 'id',
                    name: 'productos.id',
                    width: '30px'
                },
                {
                    data: 'nombre',
                    name: 'nombre',
                    title: 'Nombre'
                },
                {
                    data: 'marca',
                    name: 'marca.nombre',
                    title: 'Marca',
                },
                {
                    data: 'codigo_qr',
                    name: 'codigo_qr',
                    title: 'Código QR'
                },
                {
                    data: 'clase',
                    name: 'clase',
                    title: 'Clase'
                },
                {
                    data: 'grupo',
                    name: 'grupo.nombre',
                    title: 'Grupo'
                },
                @can('productos.ver_valor_compra')
                    {
                        data: 'valor_compra',
                        name: 'valor_compra',
                        title: 'Valor Compra'
                    },
                @endcan
                @can('productos.ver_valor_alquiler')
                    {
                        data: 'valor_alquiler',
                        name: 'valor_alquiler',
                        title: 'Valor Alquiler'
                    },
                @endcan {
                    data: 'activo',
                    name: 'activo',
                    title: 'Activo',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        };
    </script>
@endsection
