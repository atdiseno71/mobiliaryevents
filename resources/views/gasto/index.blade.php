@extends('layouts.app')

@section('template_title')
    Gasto
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Administar gastos') }}
                            </span>

                            <span class="card_icon"></span>
                            <div class="float-right">
                                <a href="{{ route('gastos.create') }}" class="btn btn-primary btn-sm float-right"
                                    data-placement="left">
                                    {{ __('Nuevo gasto') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @include('layouts.mensaje-error')
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>

                                        <th>Descripción</th>
                                        <th>Valor</th>
                                        <th>Fecha</th>
                                        <th>Tipo de gasto</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gastos as $gasto)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td>{{ $gasto->descripcion }}</td>
                                            <td>{{ $gasto->valor }}</td>
                                            <td>{{ date_create($gasto->fecha_gasto)->format('d-m-Y') }}</td>
                                            <td>{{ $gasto->tipoGasto->nombre }}</td>

                                            <td>
                                                <form action="{{ route('gastos.destroy', $gasto->id) }}" method="POST"
                                                    class="form-delete">
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('gastos.edit', $gasto->id) }}"><i
                                                            class="fa fa-fw fa-edit"></i></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fa fa-fw fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $gastos->links() !!}
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/plugins/sweetalert.js') }}"></script>
@endsection
