<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\Auth;

class InventarioService
{
    public static function obtenerInventario($producto_id, $almacen_id)
    {
        return Inventario::firstOrCreate(
            ['producto_id' => $producto_id, 'almacen_id' => $almacen_id],
            ['stock' => 0, 'created_by' => Auth::id(), 'activo' => true]
        );
    }

    public static function mover($producto_id, $almacen_id, $cantidad, $tipo, $motivo = null, $refId = null, $refTipo = null)
    {
        if ($cantidad <= 0) {
            throw new \Exception("La cantidad debe ser mayor a cero.");
        }

        $inventario = self::obtenerInventario($producto_id, $almacen_id);

        if ($tipo === 'salida' && $inventario->stock < $cantidad) {
            throw new \Exception("Stock insuficiente.");
        }

        // actualizamos stock
        $inventario->stock += ($tipo === 'ingreso') ? $cantidad : -$cantidad;
        $inventario->save();

        // registramos movimiento
        MovimientoInventario::create([
            'inventario_id' => $inventario->id,
            'producto_id' => $producto_id,
            'almacen_id' => $almacen_id,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'referencia_id' => $refId,
            'referencia_tipo' => $refTipo,
            'created_by' => Auth::id()
        ]);

        return $inventario;
    }
}
