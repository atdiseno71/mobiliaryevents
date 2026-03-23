<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionDetalle extends Model
{
    public static $rules = [
        'cotizacion_id' => 'required|exists:cotizaciones,id',
        'cantidad' => 'required|integer|min:1',
        'producto_id' => 'nullable|exists:productos,id',
        'combinacion_id' => 'nullable|exists:combinaciones,id',
        'referencia_id' => 'nullable|exists:subreferencias,id',
    ];

    protected $table = 'cotizaciones_detalle';

    protected $perPage = 20;

    protected $fillable = [
        'cotizacion_id',
        'producto_id',
        'combinacion_id',
        'referencia_id',
        'cantidad',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function combinacion()
    {
        return $this->belongsTo(Combinacion::class, 'combinacion_id');
    }

    public function referencia()
    {
        return $this->belongsTo(SubReferencia::class, 'referencia_id');
    }
}