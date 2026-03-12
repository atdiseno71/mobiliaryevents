<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemisionDetalle extends Model
{

    static $rules = [
        'remision_id' => 'required',
        'producto_id' => 'required',
        'cantidad' => 'required',
    ];

    protected $table = 'remisiones_detalle';

    protected $perPage = 20;

    protected $fillable = [
        'remision_id',
        'producto_id',
        'combinacion_id',
        'referencia_id',
        'cantidad',
    ];

    /* ============================
     *         RELACIONES
     * ============================ */

    public function remision()
    {
        return $this->belongsTo(Remision::class, 'remision_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function combinacion()
    {
        return $this->belongsTo(Combinacion::class);
    }

    public function referencia()
    {
        return $this->belongsTo(SubReferencia::class, 'referencia_id');
    }
}
