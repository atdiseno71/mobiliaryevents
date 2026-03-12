<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Remisión #{{ $remision->consecutivo ?? $remision->id }}</title>

    <style>
        @page {
            margin: 150px 40px 20px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
        }

        .header-bg {
            position: fixed;

            top: -150px;
            left: -40px;
            right: -40px;

            height: 140px;
            width: auto;

            background-image: url('{{ public_path('img/fondo-pdf.png') }}');
            background-repeat: no-repeat;
            background-size: contain;
            background-position: left top;

            z-index: -1;
        }

        .content {
            padding: 0;
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

        tr {
            page-break-inside: avoid;
        }

        /* Total */
        .total-wrap {
            margin-top: 15px;
            width: 100%;
            border-collapse: collapse;
        }

        .total-wrap td {
            border: 1px solid #aaa;
            padding: 8px;
            font-weight: bold;
        }

        .total-label {
            text-align: right;
            background: #f7f7f7;
            width: 70%;
        }

        .total-value {
            text-align: right;
            width: 30%;
        }
    </style>
</head>

<body>

    <div class="header-bg"></div>

    <div class="content">

        <h3 style="text-align: center;">REMISIÓN DE EQUIPOS</h3>

        <table>
            <tr>
                <th style="width: 25%;">EMPRESA</th>
                <td>{{ $remision->cliente->nombre ?? '—' }}</td>
                <th>FECHA EVENTO</th>
                <td>{{ $remision->fecha_evento ? \Carbon\Carbon::parse($remision->fecha_evento)->format('Y-m-d h:i A') : '—' }}
                </td>
            </tr>
            <tr>
                <th>CONTACTO</th>
                <td>{{ $remision->contacto }}</td>
                <th>FECHA MONTAJE</th>
                <td>{{ $remision->fecha_montaje ? \Carbon\Carbon::parse($remision->fecha_montaje)->format('Y-m-d h:i A') : '—' }}
                </td>
            </tr>
            <tr>
                <th>CIUDAD</th>
                <td>{{ $remision->ciudad?->nombre }}</td>
                <th>PERSONAL</th>
                <td>{{ implode(', ', $remision->personal_nombres) }}</td>
            </tr>
            <tr>
                <th>TIPO DE EVENTO</th>
                <td>{{ $remision->tipoEvento?->nombre }}</td>
                <th>TRANSPORTE</th>
                <td>{{ $remision->transporte }}</td>
            </tr>
            <tr>
                <th>DESTINO</th>
                <td colspan="3">{{ $remision->destino }}</td>
            </tr>
        </table>

        @php
            $grupos = [];

            foreach ($remision->detalles as $det) {
                if (!is_null($det->combinacion_id)) {
                    $combo = $det->combinacion;
                    $nombre = 'Combo: ' . ($combo->nombre ?? '—');
                    $primerProducto = $combo?->productos?->first();
                    $grupoId = $primerProducto?->grupo?->id ?? 9999;
                    $grupoNombre = $primerProducto?->grupo?->nombre ?? 'SIN GRUPO';
                } elseif (!is_null($det->referencia_id)) {
                    $ref = $det->referencia;
                    $nombre = $ref->nombre ?? '—';
                    $primerProducto = $ref?->productos?->first();
                    $grupoId = $primerProducto?->grupo?->id ?? 9999;
                    $grupoNombre = $primerProducto?->grupo?->nombre ?? 'SIN GRUPO';
                } else {
                    $prod = $det->producto;
                    $nombre = $prod->nombre ?? '—';
                    $grupoId = $prod?->grupo?->id ?? 9999;
                    $grupoNombre = $prod?->grupo?->nombre ?? 'SIN GRUPO';
                }

                if (!isset($grupos[$grupoId])) {
                    $grupos[$grupoId] = [
                        'nombre' => $grupoNombre,
                        'items' => [],
                    ];
                }

                $grupos[$grupoId]['items'][] = [
                    'nombre' => $nombre,
                    'cantidad' => (int) ($det->cantidad ?? 1),
                ];
            }

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
                            {{ $g['nombre'] }}
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

        {{-- TOTAL REMISIÓN --}}
        <table class="total-wrap">
            <tr>
                <td class="total-label">TOTAL REMISIÓN</td>
                <td class="total-value">
                    $ {{ number_format((float) ($remision->total ?? 0), 2, ',', '.') }}
                </td>
            </tr>
        </table>

        <p class="mt-20" style="font-size: 10px; color:#777;">
            Documento generado automáticamente - {{ now()->format('Y-m-d H:i') }}
        </p>

    </div>

</body>

</html>
