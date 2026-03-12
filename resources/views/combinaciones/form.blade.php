<div class="box box-info padding-1">
    <div class="box-body">

        {{-- Nombre --}}
        <div class="form-group">
            {{ Form::label('nombre') }}
            {{ Form::text('nombre', $data->nombre, [
                'class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''),
                'placeholder' => 'Nombre',
                'disabled' => $disabled ? 'disabled' : null,
            ]) }}
            {!! $errors->first('nombre', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- Codigo QR --}}
        <div class="form-group">
            {{ Form::label('codigo_qr') }}
            {{ Form::text('codigo_qr', $data->codigo_qr, [
                'class' => 'form-control' . ($errors->has('codigo_qr') ? ' is-invalid' : ''),
                'placeholder' => 'Código QR',
                'disabled' => $disabled ? 'disabled' : null,
            ]) }}
            {!! $errors->first('codigo_qr', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <hr>

        {{-- Equipos-Insumos --}}
        <h5 class="mb-3">Equipos-Insumos del combo</h5>

        <div class="row">
            {{-- Izquierda: disponibles --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <strong class="mb-0">Grupo </strong>
                            </div>

                            <!-- Filtro por grupo -->
                            <div class="col-md-9">
                                @if (!$disabled)
                                    {{ Form::select('grupo_id', $grupos ?? [], $data->grupo_id ?? null, [
                                        'class' => 'form-select form-select-sm select2' . ($errors->has('grupo_id') ? ' is-invalid' : ''),
                                        'placeholder' => 'Buscar por grupo...',
                                        'id' => 'buscarPorGrupo',
                                        'disabled' => $disabled ? 'disabled' : null,
                                    ]) }}
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <strong class="mb-0">Equipos-Insumos </strong>
                            </div>
                            <!-- Filtro por producto -->
                            <div class="col-md-9 d-flex align-items-center gap-2">
                                @if (!$disabled)
                                    <input type="text" id="buscarProducto" class="form-control form-control-sm"
                                        placeholder="Buscar por subreferencia...">
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                            <table class="table table-sm table-bordered mb-0" id="tabla-productos">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width:70px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productos as $p)
                                        <tr class="prod-row" data-id="{{ $p->id }}"
                                            data-name="{{ strtolower($p->nombre) }}"
                                            data-code="{{ strtolower($p->codigo_qr ?? '') }}"
                                            data-grupo="{{ $p->grupo_id }}">
                                            <td>
                                                <div class="fw-semibold">{{ $p->nombre }}</div>
                                                <small class="text-muted">{{ $p->codigo_qr }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if (!$disabled)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary add-prod"
                                                        data-id="{{ $p->id }}"
                                                        data-nombre="{{ $p->nombre }}"
                                                        data-codigo="{{ $p->codigo_qr }}">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if ($productos->isEmpty())
                                <div class="text-center text-muted py-4">No hay productos.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Derecha: seleccionados --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <strong>Seleccionados</strong>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="tabla-seleccionados">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width:70px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($preselected) && $preselected->count())
                                        @foreach ($preselected as $sp)
                                            <tr data-id="{{ $sp['id'] }}">
                                                <td>
                                                    <div class="fw-semibold">{{ $sp['nombre'] }}</div>
                                                    <small class="text-muted">{{ $sp['codigo_qr'] }}</small>

                                                    <input type="hidden" name="productos[]"
                                                        value="{{ $sp['id'] }}">
                                                </td>
                                                <td class="text-center">
                                                    @if (!$disabled)
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-prod">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="empty-sel">
                                            <td colspan="2" class="text-center text-muted py-3">No hay productos
                                                seleccionados.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {!! $errors->first('productos', '<div class="text-danger small mt-2">:message</div>') !!}
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if (!$disabled)
        @include('layouts.components.form.submit-btn')
    @endif
</div>
