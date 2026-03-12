<div class="box box-info padding-1">
    <div class="box-body">
        <div class="form-group">
            {{ Form::label('id_gasto', 'Tipo de gasto') }}
            {{ Form::select('id_gasto', $tipoGastos, $gasto->id_gasto,['class' => 'form-control select2' . ($errors->has('id_gasto') ? ' is-invalid' : '')]) }}
            {!! $errors->first('id_gasto', '<p class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('descripcion') }}
            {{ Form::text('descripcion', $gasto->descripcion, ['class' => 'form-control' . ($errors->has('descripcion') ? ' is-invalid' : ''), 'placeholder' => 'Descripcion']) }}
            {!! $errors->first('descripcion', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('valor') }}
            {{ Form::number('valor', $gasto->valor, ['class' => 'form-control' . ($errors->has('valor') ? ' is-invalid' : ''), 'placeholder' => 'Valor']) }}
            {!! $errors->first('valor', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary btn-block">Enviar</button>
    </div>
</div>
