<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoRemision extends Model
{

    protected $table = 'movimientos_remision';

    protected $fillable = [
        'almacen_id',
        'remision_id',
        'tipo',
        'motivo',
        'created_by',
        'estado_id'
    ];

    public static $rules = [
        'tipo' => 'required|in:ingreso,salida',
        'almacen_id' => 'required|exists:almacenes,id',
        'remision_id' => 'required|exists:remisiones,id',
        'estado_id' => 'required|exists:estados,id',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function remision()
    {
        return $this->belongsTo(Remision::class);
    }

    public function movimientosinventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'movimientos_remision_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }
}
