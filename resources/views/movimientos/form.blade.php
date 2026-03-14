<div class="box box-info padding-1">
    <div class="box-body">

        {{-- DATOS PRINCIPALES DE LA REMISIÓN --}}
        {{-- <h5 class="mb-3">Datos de la remisión</h5> --}}

        @if (auth()->user()->can('movimientos.editar_estados'))
            <div class="row mb-4">
                <div class="col-md-12">
                    {{ Form::label('estado_id', 'Estado') }}
                    {{ Form::select('estado_id', $estados ?? [], $data->estado_id ?? null, [
                        'class' => 'form-select select2' . ($errors->has('estado_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione un estado',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('estado_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
        @endif
        <div class="row mb-4">
            <div class="col-md-4">
                {{ Form::label('almacen_id', 'Almacen *') }}
                {{ Form::select('almacen_id', $almacenes ?? [], $data->almacen_id ?? null, [
                    'class' => 'form-select select2' . ($errors->has('almacen_id') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione una almacen',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('almacen_id', '<div class="invalid-feedback">:message</div>') !!}
            </div>
            <div class="col-md-4">
                {{ Form::label('tipo', 'Tipo *') }}
                {{ Form::select('tipo', ['ingreso' => 'Ingreso', 'salida' => 'Salida'], $data->tipo ?? null, [
                    'class' => 'form-select select2' . ($errors->has('tipo') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione tipo',
                    'disabled' => $disabledremision ? 'disabled' : null,
                ]) }}
                @if ($disabledremision)
                    {{ Form::hidden('tipo', $data->tipo) }}
                @endif
                {!! $errors->first('tipo', '<div class="invalid-feedback">:message</div>') !!}
            </div>
            <div class="col-md-4">
                {{ Form::label('remision_id', 'Remisión *') }}
                {{ Form::select('remision_id', $remisiones ?? [], $data->remision_id ?? null, [
                    'class' => 'form-select select2' . ($errors->has('remision_id') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione una remisión',
                    'id' => 'remision_id',
                    'disabled' => $disabledremision ? 'disabled' : null,
                ]) }}
                @if ($disabledremision)
                    {{ Form::hidden('remision_id', $data->remision_id) }}
                @endif
                {!! $errors->first('remision_id', '<div class="invalid-feedback">:message</div>') !!}
            </div>
        </div>

        @if ($errors->has('productos') || $errors->has('productos.*'))
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->get('productos') as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach

                    @foreach ($errors->get('productos.*') as $arr)
                        @foreach ($arr as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($errors->has('cantidades') || $errors->has('cantidades.*'))
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->get('cantidades') as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach

                    @foreach ($errors->get('cantidades.*') as $arr)
                        @foreach ($arr as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif
        <hr>

        {{-- SECCIÓN DE PRODUCTOS SELECCIONADOS EN REMISION --}}
        <h5 class="mb-3">Productos en remisión</h5>

        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="tabla-remision">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                {{-- <th>Código QR</th> --}}
                                <th>Cant.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grupos = [];

                                foreach ($data->remision->detalles ?? [] as $det) {
                                    $grupoId = 9999;
                                    $grupoNombre = 'SIN GRUPO';
                                    $nombre = '';
                                    $cantidad = $det->cantidad ?? 1;

                                    if ($det->producto) {
                                        $grupoId = $det->producto->grupo->id ?? 9999;
                                        $grupoNombre = $det->producto->grupo->nombre ?? 'SIN GRUPO';
                                        $nombre = $det->producto->nombre;
                                    } elseif ($det->referencia) {
                                        $productoAsociado = $det->referencia->productos->first();
                                        $grupoId = $productoAsociado->grupo->id ?? 9999;
                                        $grupoNombre = $productoAsociado->grupo->nombre ?? 'SIN GRUPO';
                                        $nombre = $det->referencia->nombre;
                                    }

                                    if (!isset($grupos[$grupoId])) {
                                        $grupos[$grupoId] = [
                                            'nombre' => $grupoNombre,
                                            'items' => [],
                                        ];
                                    }

                                    $grupos[$grupoId]['items'][] = [
                                        'nombre' => $nombre,
                                        'cantidad' => $cantidad,
                                    ];
                                }

                                ksort($grupos);
                            @endphp

                            @if (empty($grupos))
                                <tr class="empty-remision">
                                    <td colspan="2" class="text-center text-muted py-2">
                                        No se ha cargado ninguna remisión.
                                    </td>
                                </tr>
                            @else
                                @foreach ($grupos as $grupo)
                                    <tr>
                                        <td colspan="2"
                                            style="background:#ffe800;font-weight:bold;text-transform:uppercase;padding:6px;">
                                            {{ $grupo['nombre'] }}
                                        </td>
                                    </tr>

                                    @foreach ($grupo['items'] as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $item['nombre'] }}</div>
                                            </td>
                                            <td class="text-center">
                                                {{ $item['cantidad'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                        {{-- <tbody>
                            <tr class="empty-remision">
                                <td colspan="2" class="text-center text-muted py-2">
                                    No se ha cargado ninguna remisión.
                                </td>
                            </tr>
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>

        <hr>

        <hr>
        {{-- SECCIÓN DE PRODUCTOS --}}
        <h5 class="mb-3">Seleccionar productos</h5>

        <div class="row">
            {{-- IZQUIERDA --}}
            <div class="col-md-6">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <strong class="mb-0">Grupo</strong>
                            </div>
                            <div class="col-md-9">
                                @if (!$disabled)
                                    {{ Form::select('grupo_id', $grupos ?? [], null, [
                                        'class' => 'form-select form-select-sm select2',
                                        'placeholder' => 'Buscar por grupo...',
                                        'id' => 'buscarPorGrupo',
                                    ]) }}
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <strong class="mb-0">Productos</strong>
                            </div>
                            <div class="col-md-9">
                                @if (!$disabled)
                                    <input type="text" id="buscarProducto" class="form-control form-control-sm"
                                        placeholder="Buscar por nombre / QR...">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 520px; overflow-y: auto;"
                            id="contenedor-disponibles">

                            {{-- ===================== COMBOS ===================== --}}
                            @if (isset($combos) && $combos->isNotEmpty())
                                <div class="mb-2">
                                    <strong class="text-muted">Combos</strong>
                                </div>

                                @foreach ($combos as $combo)
                                    @php
                                        $grupoId = $combo->productos->first()?->grupo_id;
                                        $collapseId = 'combo_' . $combo->id;
                                        $comboSearch = strtolower(
                                            trim(($combo->nombre ?? '') . ' ' . ($combo->codigo_qr ?? '')),
                                        );
                                    @endphp

                                    <div class="product-group combo-group mb-2 product-row" data-tipo="combo"
                                        data-grupo="{{ $grupoId }}" data-name="{{ $comboSearch }}"
                                        data-code="{{ strtolower($combo->codigo_qr ?? '') }}">

                                        <div
                                            class="bg-light p-2 border d-flex justify-content-between align-items-center">
                                            <button class="btn btn-link text-start p-0 fw-bold" type="button"
                                                data-toggle="collapse" data-target="#{{ $collapseId }}">
                                                <i class="fas fa-caret-right me-1"></i>
                                                {{ $combo->nombre }}
                                                <small class="text-muted">(1)</small>
                                            </button>

                                            @if (!$disabled)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary agregar-producto"
                                                    data-id="{{ $combo->id }}" data-nombre="{{ $combo->nombre }}"
                                                    data-codigo_qr="{{ $combo->codigo_qr ?? '' }}"
                                                    data-inventario="combo" data-is_insumo="0" data-is_combo="1"
                                                    data-cantidad="999999" data-stocks='@json($combo->stock_por_almacen ?? [])'>
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="collapse" id="{{ $collapseId }}">
                                            <ul class="list-group list-group-flush border">
                                                @foreach ($combo->productos as $p)
                                                    <li class="list-group-item small text-muted">
                                                        {{ $p->nombre }} — {{ $p->codigo_qr }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach

                                <hr class="my-2">
                            @endif
                            {{-- =================== FIN COMBOS ==================== --}}

                            {{-- Agrupar productos por inventario_por_serie --}}
                            @php
                                $por_serie = $productos->where('inventario_por_serie', true);
                                $sin_serie = $productos->where('inventario_por_serie', false);

                                $gruposPorSubref = $por_serie
                                    ->filter(fn($p) => $p->subreferencia)
                                    ->groupBy(fn($p) => $p->subreferencia->nombre);

                                $sinSubref = $por_serie->filter(fn($p) => !$p->subreferencia);
                            @endphp

                            {{-- GRUPOS CON SUBREFERENCIA --}}
                            @if ($gruposPorSubref->isNotEmpty())
                                @foreach ($gruposPorSubref as $subref => $items)
                                    @php
                                        $padre = $items->first();
                                        $collapseId = 'grupo_' . Str::slug($subref) . '_' . $padre->id;
                                        $cantidadHijos = $items->count();
                                    @endphp

                                    <div class="product-group mb-3" data-grupo="{{ $padre->grupo_id }}"
                                        data-cantidad="{{ $cantidadHijos }}">

                                        <div
                                            class="bg-light p-2 border d-flex justify-content-between align-items-center">
                                            <button class="btn btn-link text-start p-0 fw-bold toggle-subref"
                                                type="button" data-toggle="collapse"
                                                data-target="#{{ $collapseId }}">
                                                <i class="fas fa-caret-right me-1"></i>
                                                {{ $subref }}
                                                <small class="text-muted">({{ $cantidadHijos }})</small>
                                            </button>

                                            @if (!$disabled && $padre->subreferencia)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary agregar-producto"
                                                    data-id="{{ $padre->subreferencia->id }}"
                                                    data-nombre="{{ $subref }}"
                                                    data-codigo_qr="{{ $padre->codigo_qr }}"
                                                    data-stocks='@json($padre->stock_por_almacen ?? [])' data-inventario="1"
                                                    data-is_insumo="0" data-is_combo="0"
                                                    data-cantidad="{{ $cantidadHijos }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="collapse mt-0" id="{{ $collapseId }}">
                                            <ul class="list-group list-group-flush border">
                                                @foreach ($items as $p)
                                                    @php
                                                        $esInsumo = (int) ($p->is_clase_insumo ?? 0);
                                                        $stock =
                                                            (int) (($p->stock_por_almacen ?? [])[
                                                                $data->almacen_id ?? 0
                                                            ] ?? 0);
                                                        $nameSearch = strtolower(
                                                            trim(($p->nombre ?? '') . ' ' . ($p->codigo_qr ?? '')),
                                                        );
                                                    @endphp

                                                    <li class="list-group-item d-flex justify-content-between align-items-center product-row"
                                                        data-name="{{ $nameSearch }}"
                                                        data-grupo="{{ $p->grupo_id }}">

                                                        <div>
                                                            <div>{{ $p->nombre }}</div>
                                                            <small class="text-muted">{{ $p->codigo_qr }}</small>
                                                        </div>

                                                        <div><span class="text-muted small">-</span></div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            {{-- PRODUCTOS POR SERIE SIN SUBREFERENCIA --}}
                            @if ($sinSubref->isNotEmpty())
                                @foreach ($sinSubref as $p)
                                    @php
                                        $esInsumo = (int) ($p->is_clase_insumo ?? 0);
                                        $nameSearch = strtolower(
                                            trim(($p->nombre ?? '') . ' ' . ($p->codigo_qr ?? '')),
                                        );
                                    @endphp

                                    <li class="list-group-item d-flex justify-content-between align-items-center product-row"
                                        data-name="{{ $nameSearch }}" data-grupo="{{ $p->grupo_id }}">

                                        <div>
                                            <div class="fw-semibold">{{ $p->nombre }}</div>
                                            <small class="text-muted">{{ $p->codigo_qr }} —
                                                {{ $p->marca->nombre ?? '' }}</small>
                                        </div>

                                        <div>
                                            @if (!$disabled)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary agregar-producto"
                                                    data-id="{{ $p->id }}" data-nombre="{{ $p->nombre }}"
                                                    data-codigo_qr="{{ $p->codigo_qr }}"
                                                    data-stocks='@json($p->stock_por_almacen ?? [])' data-inventario="1"
                                                    data-is_insumo="{{ $esInsumo }}" data-is_combo="0"
                                                    data-cantidad="999999">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                            {{-- PRODUCTOS SIN SERIE --}}
                            @if ($sin_serie->isNotEmpty())
                                @foreach ($sin_serie as $p)
                                    @php
                                        $esInsumo = (int) ($p->is_clase_insumo ?? 0);
                                        $nameSearch = strtolower(
                                            trim(($p->nombre ?? '') . ' ' . ($p->codigo_qr ?? '')),
                                        );
                                    @endphp

                                    <li class="list-group-item d-flex justify-content-between align-items-center product-row"
                                        data-name="{{ $nameSearch }}" data-grupo="{{ $p->grupo_id }}">

                                        <div>
                                            <div class="fw-semibold">{{ $p->nombre }}</div>
                                            <small class="text-muted">{{ $p->codigo_qr }} —
                                                {{ $p->marca->nombre ?? '' }}</small>
                                        </div>

                                        <div>
                                            @if (!$disabled)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary agregar-producto"
                                                    data-id="{{ $p->id }}" data-nombre="{{ $p->nombre }}"
                                                    data-codigo_qr="{{ $p->codigo_qr }}"
                                                    data-stocks='@json($p->stock_por_almacen ?? [])' data-inventario="0"
                                                    data-is_insumo="{{ $esInsumo }}" data-is_combo="0"
                                                    data-cantidad="999999">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                            @if ($productos->isEmpty() && (!isset($combos) || $combos->isEmpty()))
                                <div class="text-center text-muted py-4">No hay productos.</div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- DERECHA --}}
            <div class="col-md-6">

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Productos seleccionados</strong>
                        @if (!$disabled)
                            <small class="text-muted">Cant. & acciones</small>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="productos-seleccionados">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width: 110px;">Cant.</th>
                                        <th style="width: 60px;">Acción</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if (isset($data) && $data->detalles && $data->detalles->isNotEmpty())
                                        @foreach ($data->detalles as $det)
                                            @php
                                                $isCombo = !is_null($det->combinacion_id);
                                                $isSerie = (bool) $det->referencia_id;

                                                $rowId = $isCombo
                                                    ? $det->combinacion_id
                                                    : ($isSerie
                                                        ? $det->referencia_id
                                                        : $det->producto_id);

                                                $inv = $isCombo ? 'combo' : ($isSerie ? '1' : '0');

                                                $isInsumo = (int) ($det->producto?->is_clase_insumo ?? 0);
                                                $isComboFlag = $isCombo ? '1' : '0';

                                                $max = 999999;
                                            @endphp

                                            <tr data-key="{{ $rowId }}__inv{{ $inv }}"
                                                data-id="{{ $rowId }}" data-inv="{{ $inv }}"
                                                data-max="{{ $max }}">
                                                <td>
                                                    @if ($isCombo)
                                                        <strong>{{ $det->combinacion->nombre ?? 'Combo' }}</strong>
                                                        <div><small class="text-muted">Combo</small></div>
                                                    @elseif ($isSerie)
                                                        {{ $det->referencia->nombre ?? '—' }}
                                                        <div><small
                                                                class="text-muted">{{ $det->referencia->codigo ?? '' }}</small>
                                                        </div>
                                                    @else
                                                        {{ $det->producto->nombre ?? '—' }}
                                                        <div><small
                                                                class="text-muted">{{ $det->producto->codigo_qr ?? '' }}</small>
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="align-middle">
                                                    <input type="hidden" name="productos[]"
                                                        value="{{ $rowId }}">
                                                    <input type="hidden" name="inventarios[]"
                                                        value="{{ $inv }}">
                                                    <input type="hidden" name="is_insumos[]"
                                                        value="{{ $isInsumo }}">
                                                    <input type="hidden" name="is_combos[]"
                                                        value="{{ $isComboFlag }}">

                                                    <input type="number" name="cantidades[]"
                                                        value="{{ $det->cantidad ?? 1 }}" min="1"
                                                        class="form-control form-control-sm cantidad-input"
                                                        {{ $disabled ? 'disabled' : '' }}>
                                                </td>

                                                <td class="align-middle text-center">
                                                    @if (!$disabled)
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm quitar-producto">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="empty-placeholder">
                                            <td colspan="3" class="text-center text-muted py-4">
                                                No hay productos seleccionados.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if (!$disabled)
                            <small class="text-muted">Puedes ajustar cantidades tipo insumo antes de guardar.</small>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="form-group">
                    {{ Form::label('motivo', 'Observaciones') }}
                    {{ Form::textArea('motivo', $data->motivo, ['class' => 'form-control' . ($errors->has('motivo') ? ' is-invalid' : ''), 'placeholder' => 'motivo']) }}
                    {!! $errors->first('motivo', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
        </div>

        @if (!$disabled)
            @include('layouts.components.form.submit-btn')
        @endif
    </div>
