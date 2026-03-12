<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Remisión #{{ $remision->consecutivo ?? $remision->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2 {
            margin-bottom: 0;
            font-size: 18px;
        }

        h3 {
            margin-top: 4px;
            font-size: 15px;
        }

        .subtitulo {
            margin-top: 0;
            font-size: 12px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 6px;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .grupo-header {
            background: #ffe800;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <h2>REMISIÓN #{{ $remision->consecutivo ?? $remision->id }}</h2>
    <h3>REMISIÓN DE EQUIPOS</h3>
    <p class="subtitulo">FECHA: {{ $remision->created_at->format('Y-m-d') }}</p>

    <table>
        <tr>
            <th style="width: 25%;">EMPRESA</th>
            <td>{{ $remision->cliente->nombre ?? '—' }}</td>
        </tr>
        <tr>
            <th>CONTACTO</th>
            <td>{{ $remision->contacto }}</td>
        </tr>
        <tr>
            <th>CIUDAD</th>
            <td>{{ $remision->ciudad?->nombre }}</td>
        </tr>
        <tr>
            <th>TIPO DE EVENTO</th>
            <td>{{ $remision->tipoEvento?->nombre }}</td>
        </tr>
        <tr>
            <th>FECHA EVENTO</th>
            <td>{{ $remision->fecha_evento ? \Carbon\Carbon::parse($remision->fecha_evento)->format('Y-m-d h:i A') : '—' }}
            </td>
        </tr>
        <tr>
            <th>FECHA MONTAJE</th>
            <td>{{ $remision->fecha_montaje ? \Carbon\Carbon::parse($remision->fecha_montaje)->format('Y-m-d h:i A') : '—' }}
            </td>
        </tr>
        <tr>
            <th>PERSONAL</th>
            <td>{{ implode(', ', $remision->personal_nombres) }}</td>
        </tr>
        <tr>
            <th>TRANSPORTE</th>
            <td>{{ $remision->transporte }}</td>
        </tr>
        <tr>
            <th>DESTINO</th>
            <td>{{ $remision->destino }}</td>
        </tr>
    </table>
    @php
        $grupos = [];

        foreach ($remision->detalles as $det) {
            // Determinar si es referencia o producto normal
            if ($det->referencia_id) {
                $ref = $det->referencia;
                $nombre = $ref->nombre;

                // Tomamos el primer producto asociado para saber el grupo
                $primerProducto = $ref->productos->first();

                $grupoId = $primerProducto->grupo->id ?? 9999;
                $grupoNombre = $primerProducto->grupo->nombre ?? 'SIN GRUPO';
            } else {
                $prod = $det->producto;
                $nombre = $prod->nombre;

                $grupoId = $prod->grupo->id ?? 9999;
                $grupoNombre = $prod->grupo->nombre ?? 'SIN GRUPO';
            }

            // Crear grupo si no existe
            if (!array_key_exists($grupoId, $grupos)) {
                $grupos[$grupoId] = [
                    'nombre' => $grupoNombre,
                    'items' => [],
                ];
            }

            // Agregar item
            $grupos[$grupoId]['items'][] = [
                'nombre' => $nombre,
                'cantidad' => $det->cantidad,
            ];
        }

        // Ordenar por id de grupo
        ksort($grupos);
    @endphp
    <table>
        <thead>
            <tr>
                <th style="width:70%;">REQUERIMIENTO</th>
                <th style="width:30%;">CANTIDAD</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($grupos as $g)
                <tr>
                    <td colspan="2" class="grupo-header">
                        {{ $g['nombre'] }} -----
                    </td>
                </tr>

                @foreach ($g['items'] as $item)
                    <tr>
                        <td>{{ $item['nombre'] }}</td>
                        <td class="text-center">{{ $item['cantidad'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <p class="mt-20" style="font-size: 10px; color:#777;">
        Documento generado automáticamente - {{ now()->format('Y-m-d H:i') }}
    </p>

</body>

</html>
