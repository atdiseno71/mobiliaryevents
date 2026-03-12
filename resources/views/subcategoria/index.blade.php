@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Administar subcategorias') }}
                            </span>

                            <span class="card_icon"></span>
                            <div class="float-right">
                                <a href="{{ route('subcategorias.create') }}" class="btn btn-primary btn-sm float-right"
                                    data-placement="left">
                                    {{ __('Nueva subcategoria') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @include('layouts.mensaje-error')

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nombre</th>
                                        <th>Categoria</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
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

        // 🔽 Configuración compatible con tu script genérico
        window.tableConfigs['.table'] = {
            ajaxUrl: "{{ route('subcategorias.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    width: '30px',
                    title: 'No'
                },
                {
                    data: 'nombre',
                    name: 'nombre',
                    title: 'Nombre'
                },
                {
                    data: 'categoria',
                    name: 'categoria.nombre',
                    title: 'Categoria'
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
    </script>
@endsection
