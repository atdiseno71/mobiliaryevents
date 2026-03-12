<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventario extends Model
{
    use SoftDeletes;

    protected $table = 'inventarios';

    protected $fillable = [
        'producto_id',
        'almacen_id',
        'stock',
        'created_by',
    ];

    public static $rules = [
        'producto_id' => 'required|exists:productos,id',
        'almacen_id' => 'required|exists:almacenes,id',
        'stock' => 'integer|min:0',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}
