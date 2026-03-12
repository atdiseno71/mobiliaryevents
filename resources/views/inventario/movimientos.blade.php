@extends('layouts.app')

@section('template_title')
    Movimientos de Inventario
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Movimientos: {{ $inventario->producto?->nombre ?? '-' }} en
                                {{ $inventario->almacen?->nombre ?? '-' }}</span>
                            <a href="{{ route('inventarios.index') }}" class="btn btn-primary btn-sm">Volver</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Motivo</th>
                                    <th>Referencia</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($movimientos as $mov)
                                    <tr>
                                        <td>{{ $mov->id }}</td>
                                        <td><span
                                                class="badge {{ $mov->tipo == 'ingreso' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($mov->tipo) }}</span>
                                        </td>
                                        <td>{{ $mov->cantidad }}</td>
                                        <td>{{ $mov->motivo ?? '-' }}</td>
                                        <td>{{ $mov->referencia_tipo ? $mov->referencia_tipo . ' #' . $mov->referencia_id : '-' }}
                                        </td>
                                        <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No hay movimientos</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $movimientos->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
