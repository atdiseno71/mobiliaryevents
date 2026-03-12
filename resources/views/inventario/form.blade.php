<div class="box box-info padding-1">
    <div class="box-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="switch">
                    {{ Form::checkbox('activo', 1, $data->activo, ['id' => 'activo', 'disabled' => $disabled ?? false]) }}
                    <span class="slider"></span>
                </label>
                <label for="activo" class="ml-2">¿Activo?</label>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                {{ Form::label('producto_id', 'Producto *') }}
                {{ Form::select('producto_id', $productos, $data->producto_id ?? null, ['class' => 'form-select select2', 'placeholder' => 'Seleccione un producto', 'disabled' => $disabled ?? false]) }}
            </div>
            <div class="col-md-6">
                {{ Form::label('almacen_id', 'Almacén *') }}
                {{ Form::select('almacen_id', $almacenes, $data->almacen_id ?? null, ['class' => 'form-select select2', 'placeholder' => 'Seleccione un almacén', 'disabled' => $disabled ?? false]) }}
            </div>
            <div class="col-md-6 mt-3">
                {{ Form::label('stock', 'Stock *') }}
                {{ Form::number('stock', $data->stock ?? 0, ['class' => 'form-control', 'min' => 0, 'disabled' => $disabled ?? false]) }}
            </div>
        </div>
    </div>

    @if (!($disabled ?? false))
        <button type="submit" class="btn btn-primary mt-3">Guardar</button>
    @endif
</div>
