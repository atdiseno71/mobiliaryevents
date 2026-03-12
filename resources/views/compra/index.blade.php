@extends('layouts.app')

@section('plugins.DateRangePicker', true)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Administrar compras') }}
                            </span>

                            <form action="{{ route('compras.rango.pdf') }}" method="POST">
                                @csrf
                                <input id="fechaInicial" name="fechaInicial" type="hidden">
                                <input id="fechaFinal" name="fechaFinal" type="hidden">
                                <button type="button" class="btn btn-default pull-right" id="daterange-btn">

                                    <span><i class="fa fa-calendar"></i> Rango de fecha</span>

                                    <i class="fa fa-caret-down"></i>

                                </button>
                                <button type="submit" class="btn btn-primary btn-sm">Traer reporte <i class="fa fa-file"></i></button>
                            </form>
                        </div>
                    </div>
                    @include('layouts.mensaje-error')
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
										<th>Código</th>
										<th>Cliente</th>
										<th>Comprador</th>
										<th>Total</th>
										<th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($compras as $compra)
                                        <tr>
                                            <td>{{ ++$i }}</td>
											<td>{{ $compra->codigo_factura }}</td>
											<td>{{ $compra->cliente->nombre }}</td>
											<td>{{ $compra->user->name }}</td>
											<td>{{ $compra->total }}</td>
											<td>{{ date_create($compra->fecha_venta)->format('d-m-Y') }}</td>
                                            <td>
                                                <form action="{{ route('compras.destroy',$compra->id) }}" method="POST" class="form-delete">
                                                    <a class="btn btn-sm btn-primary" target="_blank" href="{{ route('compras.factura',$compra->id) }}"><i class="fa-solid fa-print"></i></a>
                                                    <a class="btn btn-sm btn-warning" href="{{ route('compras.edit', ['compra' => $compra->id]) }}"><i class="fa fa-fw fa-edit"></i></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $compras->appends(request()->except('page'))->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

    <script src="{{ asset('js/plugins/sweetalert.js') }}"></script>
    <script src="{{ asset('js/views/ventas-rango.js') }}"></script>

@endsection