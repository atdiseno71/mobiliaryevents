<div class="box box-info padding-1">
    <div class="box-body">

        <!--=====================================
        ENTRADA PARA EL CODIGO
        ======================================-->
        <div class="form-group">
            {{ Form::label('codigo_factura', 'Código') }}
            {{ Form::number('codigo_factura', $codigo, ['class' => 'form-control', 'readonly' . ($errors->has('codigo_factura') ? ' is-invalid' : '')]) }}
            {!! $errors->first('codigo_factura', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <!--=====================================
        ENTRADA DEL COMPRADOR
        ======================================-->
        <div class="form-group">
            {{ Form::label('nombre', 'Comprador o vendedor') }}
            {{ Form::hidden('id_comprador', auth()->id(), ['class' => 'form-control'. ($errors->has('id_comprador') ? ' is-invalid' : '')]) }}
            {{ Form::text('nombre', auth()->user()->name, ['class' => 'form-control', 'readonly' . ($errors->has('id_comprador') ? ' is-invalid' : '')]) }}
            {!! $errors->first('id_comprador', '<p class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {!! $errors->first('productos', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <!--=====================================
        ENTRADA PARA AGREGAR CLIENTE
        ======================================-->
        <div class="form-group">
            {{ Form::label('id_cliente', 'Cliente') }}
            {{ Form::select('id_cliente', $clientes, $compra->id_cliente,['class' => 'form-control select2' . ($errors->has('id_cliente') ? ' is-invalid' : ''), 'placeholder' => 'Seleccione un cliente']) }}
            {!! $errors->first('id_cliente', '<p class="invalid-feedback">:message</p>') !!}
        </div>
        <!--=====================================
        ENTRADA PARA AGREGAR LAS CANTIDADES
        ======================================-->
        <div class="form-group">
            {{ Form::label('tipo_cantidad', 'Tipo pesaje') }}
            {{ Form::select('tipo_cantidad', ['0'=>'Por kilo','1'=>'Por arroba'], $compra->tipo_cantidad,['class' => 'form-control select2 tipo_cantidad' . ($errors->has('tipo_cantidad') ? ' is-invalid' : ''), 'placeholder' => 'Seleccione un tipo']) }}
            {!! $errors->first('tipo_cantidad', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <!--=====================================
        GUARDAR PRODUCTOS SELECCIONADOS
        ======================================-->
        <input type="hidden" id="listaProductos" name="listaProductos" value="{{ $compra->productos }}">
        <!--=====================================
        ENTRADA PARA AGREGAR PRODUCTOS
        ======================================-->
        <div class="nuevoProducto">
            @if ($accion == 'editar')
                @php
                    $listaProducto = json_decode($compra->productos, true);
                    foreach ($listaProducto as $key => $value) {
                        echo    '<div class="row todo" style="padding:5px 15px">
                                    <div class="col-12 col-md-3">
                                        <label for="nuevaDescripcion">Seleccione un producto</label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-danger btn-xs quitarProducto input-group-addon" idProducto="'.$value['id'].'"><i class="fa fa-times"></i></button>
                                            <input type="text" class="form-control nuevaDescripcion" idProducto="'.$value['id'].'" name="nuevaDescripcion" value="'.$value['descripcion'].'" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="nuevaCantidad">Ingrese la cantidad a vender</label>
                                            <input type="number" class="form-control nuevaCantidad" name="nuevaCantidad" value="'.$value['cantidad'].'" peso="'.$value['peso'].'" step="any" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3" style="display: none;">
                                            <div class="form-group ingresoLibras">
                                            <label for="cantidadLibras">Ingrese las libras a comprar</label>
                                            <input type="number" class="form-control cantidadLibras" name="cantidadLibras" decimales="'.(($value['libras']*4)/100).'" min="0" step="any" value="'.$value['libras'].'" required>
                                        </div>
                                   </div>
                                    <div class="col-12 col-md-3">
                                        <label for="nuevaCantidad">Ingrese el precio del producto</label>
                                        <div class="form-group">
                                            <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                                            <input type="number" class="form-control nuevoPrecio" name="nuevoPrecio" value="'.$value['precio'].'" min="50"required>
                                        </div>
                                    </div>
                                </div>';
                    }
                @endphp
            @endif

        </div>
        <!--=====================================
        BOTÓN PARA AGREGAR PRODUCTO
        ======================================-->
        <button type="button" class="btn btn-success btn-block btnAgregarProducto" @if ($accion != 'editar') hidden @endif>Agregar producto</button>
        <hr>
        <!--=====================================
                TOTAL COMPRAS
        ======================================-->
        <div class="form-group">
            {{ Form::label('total') }}
            {{ Form::number('total', $compra->total, ['class' => 'form-control','readonly','required','id'=>'nuevoTotalVenta' . ($errors->has('total') ? ' is-invalid' : ''), 'placeholder' => '000000']) }}
            {!! $errors->first('total', '<div class="invalid-feedback">:message</div>') !!}
        </div>
    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary btn-block">Enviar</button>
    </div>
</div>
