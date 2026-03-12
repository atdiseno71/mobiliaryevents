@extends('layouts.app')

@section('template_title')
    User
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Administrar usuarios') }}
                            </span>

                            @can('usuarios.create')
                                <span class="card_icon"></span>
                                <div class="float-right">
                                    <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm float-right"
                                        data-placement="left">
                                        {{ __('Nuevo usuario') }}
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                    @include('layouts.mensaje-error')
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Nivel</th>
                                        <th>Estado</th>
                                        <th>Email</th>

                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay información registrada.
                                        </td>
                                    </tr>
                                    {{-- @foreach ($users as $user)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->username }}</td>

                                            <td>
                                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST"
                                                    class="form-delete">
                                                    <a class="btn btn-sm btn-primary"
                                                        href="{{ route('usuario.form.cambiar-contrasena', $user->id) }}"><i
                                                            class="fa fa-key"></i></a>
                                                    <a class="btn btn-sm btn-warning"
                                                        href="{{ route('usuarios.edit', $user->id) }}"><i
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
                {!! $users->links() !!}
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
            ajaxUrl: "{{ route('usuarios.index') }}",
            columns: [{
                    data: 'codigo',
                    name: 'codigo',
                    title: 'Código'
                },
                {
                    data: 'name',
                    name: 'name',
                    title: 'Nombre'
                },
                {
                    data: 'nivel',
                    name: 'nivel',
                    title: 'Nivel'
                },
                {
                    data: 'estado',
                    name: 'estado',
                    title: 'Estado'
                },
                {
                    data: 'email',
                    name: 'email',
                    title: 'Email'
                },
                {
                    data: 'action',
                    name: 'action',
                }
            ]
        };
    </script>
@endsection
