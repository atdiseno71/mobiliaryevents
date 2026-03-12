@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Administrar almacenes') }}
                            </span>

                            <span class="card_icon"></span>
                            <div class="float-right">
                                <a href="{{ route('almacenes.create') }}" class="btn btn-primary btn-sm float-right"
                                    data-placement="left">
                                    {{ __('Nuevo almacén') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @include('layouts.mensaje-error')

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover w-100">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Nombre</th>
                                        <th>Ciudad</th>
                                        <th>Responsable</th>
                                        <th>Activo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay información registrada.
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
            ajaxUrl: "{{ route('almacenes.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    width: '20px'
                },
                {
                    data: 'nombre',
                    name: 'nombre',
                    title: 'Nombre'
                },
                {
                    data: 'ciudad',
                    name: 'ciudad.nombre',
                    title: 'Ciudad'
                },
                {
                    data: 'responsable',
                    name: 'responsable.name',
                    title: 'Responsable'
                },
                {
                    data: 'activo',
                    name: 'activo',
                    title: 'Activo',
                    render: function(data) {
                        return data ?
                            '<span class="badge bg-success">Sí</span>' :
                            '<span class="badge bg-secondary">No</span>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                }
            ]
        };
    </script>
@endsection
