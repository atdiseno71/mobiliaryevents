<?php

namespace App\Models;

use App\Traits\ProtectRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubReferencia extends Model
{
  use ProtectRelationships;

  protected $table = 'subreferencias';

  protected $perPage = 20;

  protected $fillable = [
    'nombre',
    'subcategoria_id',
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
    return $this->hasMany(Producto::class, 'subreferencia_id');
  }

  public function subcategoria(): BelongsTo
  {
    return $this->belongsTo(SubCategoria::class, 'subcategoria_id');
  }

  /**
   * Relación: usuario que creó la marca
   */
  public function creador(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
}
