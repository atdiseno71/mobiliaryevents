<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Almacen extends Model
{
    use SoftDeletes;

    protected $table = 'almacenes';

    // Reglas de validación
    public static $rules = [
        'nombre' => 'required|string|max:120',
        'ciudad_id' => 'nullable|exists:ciudades,id',
        'responsable_id' => 'nullable|exists:users,id',
        'direccion' => 'nullable|string|max:150',
        'telefono' => 'nullable|string|max:30',
        'latitud' => 'nullable|numeric',
        'longitud' => 'nullable|numeric',
        'activo' => 'boolean'
    ];

    protected $fillable = [
        'nombre',
        'descripcion',
        'ciudad_id',
        'direccion',
        'telefono',
        'latitud',
        'longitud',
        'responsable_id',
        'activo',
        'created_by',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
    ];

    // Relaciones
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
