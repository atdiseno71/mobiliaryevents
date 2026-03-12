<div class="box box-info padding-1">
    <div class="box-body">

        <div class="row">
            <div class="col-12 col-md-6">
                <label class="switch">
                    {{ Form::hidden('activo', 0) }}
                    {{ Form::checkbox('activo', 1, $producto->activo, [
                        'id' => 'activo',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    <span class="slider"></span>
                </label>
                <label for="activo" class="ml-2">¿Activo?</label>
            </div>
            <div class="col-12 col-md-6">
                <label class="switch">
                    {{ Form::hidden('inventario_por_serie', 0) }}
                    {{ Form::checkbox('inventario_por_serie', 1, $producto->inventario_por_serie, [
                        'id' => 'inventario_por_serie',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    <span class="slider"></span>
                </label>
                <label for="inventario_por_serie" class="ml-2">¿Tiene serie?</label>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                {{-- Grupo --}}
                <div class="form-group mb-3">
                    {{ Form::label('grupo_id', 'Grupo') }}
                    {{ Form::select('grupo_id', $grupos, $producto->grupo_id, [
                        'class' => 'form-select select2' . ($errors->has('grupo_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione un grupo',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('grupo_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-12 col-md-6">
                {{-- Categoría --}}
                <div class="form-group mb-3">
                    {{ Form::label('categoria_id', 'Categoría') }}
                    {{ Form::select('categoria_id', $categorias, $producto->categoria_id, [
                        'class' => 'form-select select2' . ($errors->has('categoria_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione una categoría',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('categoria_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-12 col-md-6">
                {{-- Subcategoría --}}
                <div class="form-group mb-3">
                    {{ Form::label('subcategoria_id', 'Subcategoría') }}
                    {{ Form::select('subcategoria_id', $subcategorias, $producto->subcategoria_id, [
                        'class' => 'form-select select2' . ($errors->has('subcategoria_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione una subcategoría',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('subcategoria_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-12 col-md-6" id="subreferencia_wrap">
                {{-- Subreferencia --}}
                <div class="form-group mb-3">
                    {{ Form::label('subreferencia_id', 'Subreferencia') }}
                    {{ Form::select('subreferencia_id', $subreferencias, $producto->subreferencia_id, [
                        'class' => 'form-select select2' . ($errors->has('subreferencia_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione una subreferencia',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('subreferencia_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-12 col-md-6">
                {{-- Marca --}}
                <div class="form-group mb-3">
                    {{ Form::label('marca_id', 'Marca') }}
                    {{ Form::select('marca_id', $marcas, $producto->marca_id, [
                        'class' => 'form-select select2' . ($errors->has('marca_id') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione una marca',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('marca_id', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-12 col-md-6">
                {{-- Nombre --}}
                <div class="form-group mb-3">
                    {{ Form::label('nombre', 'Nombre *') }}
                    {{ Form::text('nombre', $producto->nombre, [
                        'class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''),
                        'placeholder' => 'Nombre del producto',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('nombre', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-12 col-md-6">
                {{-- Código QR --}}
                <div class="form-group mb-3">
                    {{ Form::label('codigo_qr', 'Código QR') }}
                    {{ Form::text('codigo_qr', $producto->codigo_qr, [
                        'class' => 'form-control' . ($errors->has('codigo_qr') ? ' is-invalid' : ''),
                        'placeholder' => 'Código QR del producto',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('codigo_qr', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-12 col-md-6">
                {{-- Clase --}}
                <div class="form-group mb-3">
                    {{ Form::label('clase', 'Clase *') }}
                    {{ Form::select('clase', ['Alquiler' => 'Alquiler', 'Insumo' => 'Insumo'], $producto->clase, [
                        'class' => 'form-select select2' . ($errors->has('clase') ? ' is-invalid' : ''),
                        'placeholder' => 'Seleccione una clase',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('clase', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            @can('productos.ver_valor_compra')
                <div class="col-12 col-md-6">
                    <div class="form-group mb-3">
                        {{ Form::label('valor_compra', 'Valor de compra') }}
                        {{ Form::number('valor_compra', $producto->valor_compra, [
                            'step' => '0.01',
                            'class' => 'form-control' . ($errors->has('valor_compra') ? ' is-invalid' : ''),
                            'placeholder' => 'Valor de compra',
                            'disabled' => $disabled ? 'disabled' : null,
                        ]) }}
                        {!! $errors->first('valor_compra', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>
            @endcan

            @can('productos.ver_valor_alquiler')
                <div class="col-12 col-md-6">
                    <div class="form-group mb-3">
                        {{ Form::label('valor_alquiler', 'Valor de alquiler') }}
                        {{ Form::number('valor_alquiler', $producto->valor_alquiler, [
                            'step' => '0.01',
                            'class' => 'form-control' . ($errors->has('valor_alquiler') ? ' is-invalid' : ''),
                            'placeholder' => 'Valor de alquiler',
                            'disabled' => $disabled ? 'disabled' : null,
                        ]) }}
                        {!! $errors->first('valor_alquiler', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>
            @endcan
            <div class="col-12 col-md-6">
                {{-- Peso unitario --}}
                <div class="form-group mb-3">
                    {{ Form::label('peso_unitario', 'Peso unitario (kg)') }}
                    {{ Form::number('peso_unitario', $producto->peso_unitario, [
                        'step' => '0.01',
                        'class' => 'form-control' . ($errors->has('peso_unitario') ? ' is-invalid' : ''),
                        'placeholder' => 'Peso del producto',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('peso_unitario', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
            <div class="col-12 col-md-6">
                {{-- Imagen --}}
                <div class="form-group mb-3">
                    {{ Form::label('imagen', 'Imagen del producto') }}
                    <div class="text-center mb-2">
                        <img id="preview_img" src="{{ $producto->imagen_url }}" class="img-thumbnail"
                            style="width: 180px; height: 180px; object-fit: cover;">
                    </div>
                    {{ Form::file('imagen', [
                        'class' => 'form-control' . ($errors->has('imagen') ? ' is-invalid' : ''),
                        'accept' => 'image/*',
                        'id' => 'imagen_input',
                        $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('imagen', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Descripción --}}
                <div class="form-group mb-3">
                    {{ Form::label('descripcion', 'Descripción') }}
                    {{ Form::textarea('descripcion', $producto->descripcion, [
                        'class' => 'form-control' . ($errors->has('descripcion') ? ' is-invalid' : ''),
                        'rows' => 3,
                        'placeholder' => 'Descripción del producto',
                        'disabled' => $disabled ? 'disabled' : null,
                    ]) }}
                    {!! $errors->first('descripcion', '<div class="invalid-feedback">:message</div>') !!}
                </div>
            </div>
        </div>
    </div>

    @if (!$disabled)
        @include('layouts.components.form.submit-btn')
    @endif
</div>
