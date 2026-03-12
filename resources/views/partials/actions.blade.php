@php
    $user = auth()->user();
    // Reglas especiales: solo Movimientos editables si estado_id == 1 o si tiene permiso especial para editar movimientos cerrados
    $canEdit =
        $user->can("{$route}.edit") &&
        ($route !== 'movimientos' || (int) $row->estado_id === 1 || $user->can("{$route}.editar_cerrados"));

    // Mostrar grupo si hay al menos una acción visible
    $hasActions =
        $user->can("{$route}.clone") ||
        $user->can("{$route}.pdf") ||
        $user->can("{$route}.show") ||
        $canEdit ||
        $user->can("{$route}.movimientos") ||
        $user->can("{$route}.mover") ||
        $user->can("{$route}.destroy");
@endphp

@if ($hasActions)
    <div class="btn-group" role="group">

        @can("{$route}.clone")
            <a href="{{ route($route . '.create', ['clone_id' => $row->id]) }}" class="btn btn-sm btn-secondary mr-1"
                title="Clonar remisión">
                <i class="fa fa-clone"></i>
            </a>
        @endcan

        @can("{$route}.pdf")
            <a href="{{ route($route . '.pdf', $row->id) }}" class="btn btn-sm btn-warning mr-1" target="_blank" title="PDF">
                <i class="fa fa-file-pdf"></i>
            </a>
        @endcan

        @can("{$route}.show")
            <a href="{{ route($route . '.show', $row->id) }}" class="btn btn-sm btn-primary mr-1" title="Ver">
                <i class="fa fa-eye"></i>
            </a>
        @endcan

        @if ($canEdit)
            <a href="{{ route($route . '.edit', $row->id) }}" class="btn btn-sm btn-success mr-1" title="Editar">
                <i class="fa fa-edit"></i>
            </a>
        @endif

        @can("{$route}.movimientos")
            <a href="{{ route($route . '.movimientos', $row->id) }}" class="btn btn-sm btn-info mr-1" title="Movimientos">
                <i class="fa fa-exchange-alt"></i>
            </a>
        @endcan

        @can("{$route}.mover")
            <button type="button" class="btn btn-sm btn-secondary mr-1" data-toggle="modal" data-target="#moverStockModal"
                data-producto="{{ $row->producto_id }}" data-almacen="{{ $row->almacen_id }}" title="Mover stock">
                <i class="fa fa-arrows-alt-h"></i>
            </button>
        @endcan

        @can("{$route}.destroy")
            <form action="{{ route($route . '.destroy', $row->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(this.closest('form'))"
                    title="Eliminar">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        @endcan

    </div>
@else
    <p class="text-muted mb-0">---</p>
@endif
