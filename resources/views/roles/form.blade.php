<div class="card shadow-sm">
    <div class="card-body">

        <div class="form-group mb-3">
            {{ Form::label('name', 'Nombre del Rol') }}
            {{ Form::text('name', $role->name ?? '', ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Ej: Administrador', 'disabled' => $disabled ? 'disabled' : null]) }}
            {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group">
            {{ Form::label('permissions', 'Permisos asignados') }}
            <div class="row">
                @foreach ($permissions as $permission)
                    <div class="col-md-4">
                        <label>
                            {{ Form::checkbox('permissions[]', $permission->name, in_array($permission->name, $rolePermissions ?? []), [
                                'disabled' => $disabled ? 'disabled' : null,
                            ]) }}
                            {{ $permission->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
    @if (!$disabled)
        @include('layouts.components.form.submit-btn')
    @endif
</div>
