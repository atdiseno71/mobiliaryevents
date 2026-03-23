@php
    $fechaEventoValue = old(
        'fecha_evento',
        !empty($data->fecha_evento) ? \Carbon\Carbon::parse($data->fecha_evento)->format('Y-m-d\TH:i') : '',
    );

    $fechaMontajeValue = old(
        'fecha_montaje',
        !empty($data->fecha_montaje) ? \Carbon\Carbon::parse($data->fecha_montaje)->format('Y-m-d\TH:i') : '',
    );

    $personalSeleccionado = old('personal_ids', $data->personal_ids_array ?? []);
@endphp

<div class="box box-info padding-1">
    <div class="box-body">

        @if (!$clonar_cotizacion)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            {{ Form::label('clonar_cotizacion_id', 'Clonar desde cotización') }}
                            {{ Form::select('clonar_cotizacion_id', $cotizacionesParaClonar ?? [], request('clone_id'), [
                                'class' => 'form-select select2',
                                'id' => 'clonar_cotizacion_id',
                                'placeholder' => 'Selecciona una cotización...',
                            ]) }}
                        </div>

                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-secondary w-100" id="btnClonarCotizacion">
                                <i class="fas fa-clone me-1"></i> Clonar
                            </button>
                        </div>
                    </div>

                    <small class="text-muted">
                        Copia la cotización seleccionada, incluyendo sus productos. El consecutivo se generará
                        automáticamente.
                    </small>
                </div>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-4">
                {{ Form::label('consecutivo', 'Consecutivo *') }}
                {{ Form::text('consecutivo', old('consecutivo', $data->consecutivo ?? ''), [
                    'class' => 'form-control' . ($errors->has('consecutivo') ? ' is-invalid' : ''),
                    'placeholder' => 'Consecutivo',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('consecutivo', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-4">
                {{ Form::label('contacto', 'Contacto') }}
                {{ Form::text('contacto', old('contacto', $data->contacto ?? ''), [
                    'class' => 'form-control' . ($errors->has('contacto') ? ' is-invalid' : ''),
                    'placeholder' => 'Nombre del contacto',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('contacto', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-4">
                {{ Form::label('cliente_id', 'Cliente') }}
                {{ Form::select('cliente_id', $clientes ?? [], old('cliente_id', $data->cliente_id ?? null), [
                    'class' => 'form-select select2' . ($errors->has('cliente_id') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione un cliente',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('cliente_id', '<div class="invalid-feedback">:message</div>') !!}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                {{ Form::label('tipo_evento_id', 'Tipo de evento') }}
                {{ Form::select('tipo_evento_id', $tipoEventos ?? [], old('tipo_evento_id', $data->tipo_evento_id ?? null), [
                    'class' => 'form-select select2' . ($errors->has('tipo_evento_id') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione tipo de evento',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('tipo_evento_id', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-4">
                {{ Form::label('ciudad_id', 'Ciudad') }}
                {{ Form::select('ciudad_id', $ciudades ?? [], old('ciudad_id', $data->ciudad_id ?? null), [
                    'class' => 'form-select select2' . ($errors->has('ciudad_id') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione ciudad',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('ciudad_id', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-4">
                {{ Form::label('estado_id', 'Estado') }}
                {{ Form::select('estado_id', $estados ?? [], old('estado_id', $data->estado_id ?? null), [
                    'class' => 'form-select select2' . ($errors->has('estado_id') ? ' is-invalid' : ''),
                    'placeholder' => 'Seleccione estado',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('estado_id', '<div class="invalid-feedback">:message</div>') !!}
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                {{ Form::label('lugar', 'Lugar') }}
                {{ Form::text('lugar', old('lugar', $data->lugar ?? ''), [
                    'class' => 'form-control' . ($errors->has('lugar') ? ' is-invalid' : ''),
                    'placeholder' => 'Lugar',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('lugar', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-3">
                {{ Form::label('fecha_evento', 'Fecha y hora del evento') }}
                {{ Form::datetimeLocal('fecha_evento', $fechaEventoValue, [
                    'class' => 'form-control' . ($errors->has('fecha_evento') ? ' is-invalid' : ''),
                    'placeholder' => 'Fecha y hora del evento',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('fecha_evento', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-3">
                {{ Form::label('fecha_montaje', 'Fecha y hora del montaje') }}
                {{ Form::datetimeLocal('fecha_montaje', $fechaMontajeValue, [
                    'class' => 'form-control' . ($errors->has('fecha_montaje') ? ' is-invalid' : ''),
                    'placeholder' => 'Fecha y hora del montaje',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('fecha_montaje', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-3">
                {{ Form::label('transporte', 'Transporte') }}
                {{ Form::text('transporte', old('transporte', $data->transporte ?? ''), [
                    'class' => 'form-control' . ($errors->has('transporte') ? ' is-invalid' : ''),
                    'placeholder' => 'Transporte',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('transporte', '<div class="invalid-feedback">:message</div>') !!}
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                {{ Form::label('placa', 'Placa') }}
                {{ Form::text('placa', old('placa', $data->placa ?? ''), [
                    'class' => 'form-control' . ($errors->has('placa') ? ' is-invalid' : ''),
                    'placeholder' => 'Placa',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('placa', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-2">
                {{ Form::label('id_conductor', 'ID Conductor') }}
                {{ Form::text('id_conductor', old('id_conductor', $data->id_conductor ?? ''), [
                    'class' => 'form-control' . ($errors->has('id_conductor') ? ' is-invalid' : ''),
                    'placeholder' => 'ID conductor',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('id_conductor', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-2">
                {{ Form::label('origen', 'Origen') }}
                {{ Form::text('origen', old('origen', $data->origen ?? ''), [
                    'class' => 'form-control' . ($errors->has('origen') ? ' is-invalid' : ''),
                    'placeholder' => 'Origen',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('origen', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-2">
                {{ Form::label('destino', 'Destino') }}
                {{ Form::text('destino', old('destino', $data->destino ?? ''), [
                    'class' => 'form-control' . ($errors->has('destino') ? ' is-invalid' : ''),
                    'placeholder' => 'Destino',
                    'disabled' => $disabled ? 'disabled' : null,
                ]) }}
                {!! $errors->first('destino', '<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="col-md-4">
                {{ Form::label('personal_ids', 'Personal') }}
                {{ Form::select('personal_ids[]', $users ?? [], $personalSeleccionado, [
                    'class' => 'form-select select2' . ($errors->has('personal_ids') ? ' is-invalid' : ''),
                    'disabled' => $disabled ? 'disabled' : null,
                    'multiple' => 'multiple',
                ]) }}
                {!! $errors->first('personal_ids', '<div class="invalid-feedback">:message</div>') !!}
            </div>
        </div>

        <hr>

        <h5 class="mb-3">Seleccionar productos</h5>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <strong class="mb-0">Grupo</strong>
                            </div>

                            <div class="col-md-9">
                                @if (!$disabled)
                                    {{ Form::select('grupo_id', $grupos ?? [], old('grupo_id', $data->grupo_id ?? null), [
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
                                <strong class="mb-0">Productos</strong>
                            </div>

                            <div class="col-md-9 d-flex align-items-center gap-2">
                                @if (!$disabled)
                                    <input type="text" id="buscarProducto" class="form-control form-control-sm"
                                        placeholder="Buscar por subreferencia o nombre...">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
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
                                        $countComboItems = $combo->productos->count();
                                    @endphp

                                    <div class="product-group combo-group mb-2" data-tipo="combo"
                                        data-grupo="{{ $grupoId }}" data-name="{{ $comboSearch }}">

                                        <div
                                            class="bg-light p-2 border d-flex justify-content-between align-items-center">
                                            <button class="btn btn-link text-start p-0 fw-bold" type="button"
                                                data-toggle="collapse" data-target="#{{ $collapseId }}">
                                                <i class="fas fa-caret-right me-1"></i>
                                                {{ $combo->nombre }}
                                                <small class="text-muted">({{ $countComboItems }} items)</small>
                                            </button>

                                            @if (!$disabled)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary agregar-producto"
                                                    data-id="{{ $combo->id }}" data-nombre="{{ $combo->nombre }}"
                                                    data-codigo_qr="{{ $combo->codigo_qr ?? '' }}"
                                                    data-inventario="combo" data-is_combo="1" data-is_insumo="0"
                                                    data-cantidad="1">
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

                            @php
                                $por_serie = $productos->where('inventario_por_serie', true);
                                $sin_serie = $productos->where('inventario_por_serie', false);

                                $productosAgrupados = $por_serie
                                    ->filter(fn($p) => $p->subreferencia)
                                    ->groupBy(fn($p) => $p->subreferencia->nombre);

                                $sinSubref = $por_serie->filter(fn($p) => !$p->subreferencia);
                            @endphp

                            @if ($productosAgrupados->isNotEmpty())
                                @foreach ($productosAgrupados as $subref => $items)
                                    @php
                                        $padre = $items->first();
                                        $collapseId =
                                            'grupo_' . \Illuminate\Support\Str::slug($subref) . '_' . $padre->id;
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
                                                    data-inventario="subreferencia" data-is_insumo="0" data-is_combo="0"
                                                    data-cantidad="{{ $cantidadHijos }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @elseif(!$disabled)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary agregar-producto"
                                                    data-id="{{ $padre->id }}" data-nombre="{{ $subref }}"
                                                    data-codigo_qr="{{ $padre->codigo_qr }}" data-inventario="0"
                                                    data-is_insumo="0" data-is_combo="0" data-cantidad="1">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="collapse mt-0" id="{{ $collapseId }}">
                                            <ul class="list-group list-group-flush border">
                                                @foreach ($items as $p)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center product-row"
                                                        data-name="{{ strtolower($p->nombre . ' ' . $p->codigo_qr) }}"
                                                        data-grupo="{{ $p->grupo_id }}">

                                                        <div>
                                                            <div>{{ $p->nombre }}</div>
                                                            <small class="text-muted">{{ $p->codigo_qr }}</small>
                                                        </div>

                                                        <div>
                                                            <span class="text-muted small">-</span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if ($sinSubref->isNotEmpty())
                                @foreach ($sinSubref as $p)
                                    <li class="list-group-item d-flex justify-content-between align-items-center product-row"
                                        data-name="{{ strtolower($p->nombre . ' ' . $p->codigo_qr) }}"
                                        data-grupo="{{ $p->grupo_id }}">

                                        @php
                                            $esInsumo = $p->is_clase_insumo;
                                            $stock = (int) ($p->stock_total ?? 0);
                                            $max = $esInsumo ? $stock : 1;
                                            $sinStock = $esInsumo && $stock <= 0;
                                        @endphp

                                        <div>
                                            <div class="fw-semibold">{{ $p->nombre }}</div>
                                            <small class="text-muted">{{ $p->codigo_qr }}
                                                {{ $p->marca->nombre ?? '' }}</small>
                                            <small class="text-muted">({{ $stock }})</small>
                                        </div>

                                        <div>
                                            @if (!$disabled)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary agregar-producto"
                                                    data-id="{{ $p->id }}" data-nombre="{{ $p->nombre }}"
                                                    data-codigo_qr="{{ $p->codigo_qr }}"
                                                    data-inventario="{{ $stock }}"
                                                    data-cantidad="{{ $max }}"
                                                    data-is_insumo="{{ $esInsumo ? '1' : '0' }}" data-is_combo="0"
                                                    {{ $sinStock ? 'disabled' : '' }}
                                                    title="{{ $esInsumo ? 'Stock: ' . $stock : '' }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                            @if ($sin_serie->isNotEmpty())
                                @foreach ($sin_serie as $p)
                                    <li class="list-group-item d-flex justify-content-between align-items-center product-row"
                                        data-name="{{ strtolower($p->nombre . ' ' . $p->codigo_qr) }}"
                                        data-grupo="{{ $p->grupo_id }}">

                                        @php
                                            $esInsumo = $p->is_clase_insumo;
                                            $stock = (int) ($p->stock_total ?? 0);
                                            $max = $esInsumo ? $stock : 1;
                                            $sinStock = $esInsumo && $stock <= 0;
                                        @endphp

                                        <div>
                                            <div class="fw-semibold">{{ $p->nombre }}</div>
                                            <small class="text-muted">{{ $p->codigo_qr }}
                                                {{ $p->marca->nombre ?? '' }}</small>
                                            <small class="text-muted">({{ $stock }})</small>
                                        </div>

                                        <div>
                                            @if (!$disabled)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary agregar-producto"
                                                    data-id="{{ $p->id }}" data-nombre="{{ $p->nombre }}"
                                                    data-codigo_qr="{{ $p->codigo_qr }}"
                                                    data-inventario="{{ $esInsumo ? $stock : 'producto_sin_serie' }}"
                                                    data-cantidad="{{ $max }}"
                                                    data-is_insumo="{{ $esInsumo ? '1' : '0' }}" data-is_combo="0"
                                                    {{ $sinStock ? 'disabled' : '' }}
                                                    title="{{ $esInsumo ? 'Stock: ' . $stock : '' }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                            @if ($productos->isEmpty())
                                <div class="text-center text-muted py-4">No hay productos.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Productos seleccionados</strong>
                        @if (!$disabled)
                            <small class="text-muted">Cant. y acciones</small>
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

                                                $isInsumo = $det->producto?->is_clase_insumo;

                                                if ($isSerie) {
                                                    $stock = $det->referencia->productos->count();
                                                } else {
                                                    $stock = $det->producto?->inventarios->sum('stock') ?? 0;
                                                }
                                            @endphp

                                            <tr data-key="{{ $rowId }}__inv{{ $inv }}"
                                                data-id="{{ $rowId }}" data-inv="{{ $inv }}"
                                                data-max="{{ $stock }}">
                                                <td>
                                                    @if ($isCombo)
                                                        <strong>{{ $det->combinacion->nombre ?? 'Combo' }}</strong>
                                                        <div>
                                                            <small class="text-muted">
                                                                Combo
                                                                ({{ $det->combinacion?->productos?->count() ?? 0 }}
                                                                items)
                                                            </small>
                                                        </div>
                                                    @elseif ($isSerie)
                                                        {{ $det->referencia->nombre ?? ($det->producto->nombre ?? '') }}
                                                        <div>
                                                            <small class="text-muted">
                                                                {{ $det->referencia->codigo ?? ($det->producto->codigo_qr ?? '') }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        {{ $det->producto->nombre ?? '' }}
                                                        <div>
                                                            <small
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
                                                        value="{{ $isInsumo ? '1' : '0' }}">
                                                    <input type="hidden" name="is_combos[]"
                                                        value="{{ $isCombo ? '1' : '0' }}">

                                                    <input type="number" name="cantidades[]"
                                                        value="{{ $det->cantidad }}" min="1"
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
                            <small class="text-muted">Puedes ajustar las cantidades antes de guardar.</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if (!$disabled)
        @include('layouts.components.form.submit-btn')
    @endif
</div>
