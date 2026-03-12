@extends('layouts.app')

@section('template_title')
    Roles
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">

                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Administrar Roles') }}
                            </span>
                            @can('roles.create')
                                <span class="card_icon"></span>
                                <div class="float-right">
                                    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm float-right"
                                        data-placement="left">
                                        {{ __('Nuevo Rol') }}
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
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Permisos</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No hay roles registrados.
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
            ajaxUrl: "{{ route('roles.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    width: '20px'
                },
                {
                    data: 'name',
                    name: 'name',
                    title: 'Nombre'
                },
                {
                    data: 'permissions',
                    name: 'permissions',
                    title: 'Permisos',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        };
    </script>
@endsection
