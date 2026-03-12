@php
    $error = Session::get('error');
    $success = Session::get('success');

    // mov_alerts puede venir como array (ideal) o como string (por si acaso)
    $movAlerts = session('mov_alerts');
    if (is_string($movAlerts) && trim($movAlerts) !== '') {
        $movAlerts = [$movAlerts];
    }
    if (!is_array($movAlerts)) {
        $movAlerts = [];
    }
@endphp

@if ($error)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div>{{ $error }}</div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($success)
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div>{{ $success }}</div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (!empty($movAlerts))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Ojo:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($movAlerts as $a)
                <li>{{ $a }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
