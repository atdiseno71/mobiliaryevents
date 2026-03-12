<?php

namespace App\Models;

use App\Traits\ProtectRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Marca extends Model
{
  use ProtectRelationships;

  protected $perPage = 20;

  protected $fillable = [
    'nombre',
    'created_by'
  ];

  static $rules = [
    'nombre' => 'required|string|max:255',
  ];

  protected array $deletionGuardedRelations = [
    'productos'
  ];

  /**
   * Relación: una marca tiene muchos productos
   */
  public function productos(): HasMany
  {
    return $this->hasMany(Producto::class, 'marca_id');
  }

  /**
   * Relación: usuario que creó la marca
   */
  public function creador(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
}
