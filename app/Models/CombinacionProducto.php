<?php

namespace App\Models;

use App\Traits\ProtectRelationships;
use Illuminate\Database\Eloquent\Model;

class CombinacionProducto extends Model
{
  use ProtectRelationships;

  protected $table = 'combinacion_productos';

  protected $perPage = 20;

  public static $rules = [
    'combinacion_id' => 'required|exists:combinaciones,id',
    'producto_id' => 'required|exists:productos,id',
  ];

  protected $fillable = [
    'combinacion_id',
    'producto_id',
  ];

  protected array $deletionGuardedRelations = [];

  public function combinacion()
  {
    return $this->belongsTo(Combinacion::class, 'combinacion_id');
  }

  public function producto()
  {
    return $this->belongsTo(Producto::class, 'producto_id');
  }
}
