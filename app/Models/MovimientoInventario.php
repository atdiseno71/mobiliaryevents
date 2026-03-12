<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'inventario_id',
        'producto_id',
        'combinacion_id',
        'almacen_id',
        'remision_id',
        'movimientos_remision_id',
        'tipo',
        'cantidad',
        'motivo',
        'referencia_id',
        'referencia_tipo',
        'created_by'
    ];

    public static $rules = [
        'tipo' => 'required|in:ingreso,salida',
        'cantidad' => 'required|integer|min:1',
        'producto_id' => 'required|exists:productos,id',
        'combinacion_id' => 'sometimes|exists:combinaciones,id',
        'almacen_id' => 'required|exists:almacenes,id',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function combinacion()
    {
        return $this->belongsTo(Combinacion::class);
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function remision()
    {
        return $this->belongsTo(Remision::class);
    }

    public function referencia()
    {
        return $this->belongsTo(SubReferencia::class);
    }

    public function movimientosRemision()
    {
        return $this->belongsTo(MovimientoRemision::class, 'movimientos_remision_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
