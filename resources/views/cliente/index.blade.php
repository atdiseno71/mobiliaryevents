@extends('layouts.app')

@section('template_title')
    Cliente
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Administrar clientes') }}
                            </span>

                            @can('clientes.create')
                                <span class="card_icon"></span>
                                <div class="float-right">
                                    <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-sm float-right"
                                        data-placement="left">
                                        {{ __('Nuevo cliente') }}
                                    </a>
                                </div>
                            @endcan
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
                                        <th>Telefonos</th>
                                        <th>Direccion</th>
                                        <th>Creado</th>

                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay clientes registrados.
                                        </td>
                                    </tr>
                                    {{-- @foreach ($clientes as $cliente)
                                        <tr>
                                            <td>{{ $cliente->id }}</td>

                                            <td>{{ $cliente->nombre }}</td>
                                            <td>{{ $cliente->telefono }}</td>
                                            <td>{{ $cliente->direccion }}</td>
                                            <td>{{ date_create($cliente->fecha_creado)->format('d-m-Y') }}</td>

                                            <td>
                                                <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST"
                                                    class="form-delete">
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('clientes.edit', $cliente->id) }}"><i
                                                            class="fa fa-fw fa-edit"></i></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fa fa-fw fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach --}}
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

    {{-- buscador --}}
    <script>
        window.defaultPageLength = {{ $perPage }};
        window.tableConfigs = window.tableConfigs || {};
        window.tableConfigs['.table'] = {
            ajaxUrl: "{{ route('clientes.index') }}",
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
                    data: 'telefonos',
                    name: 'telefonos',
                    title: 'Telefonos'
                },
                {
                    data: 'direccion',
                    name: 'direccion',
                    title: 'Dirección'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: 'Fecha creado'
                },
                {
                    data: 'action',
                    name: 'action',
                }
            ]
        };
    </script>
@endsection
