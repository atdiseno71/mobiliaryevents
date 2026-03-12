<?php

namespace App\Models;

use App\Traits\ProtectRelationships;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

  use ProtectRelationships;

  static $rules = [
    'nombre' => 'required',
    'grupo_id' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = [
    'nombre',
    'grupo_id',
    'created_by'
  ];

  protected array $deletionGuardedRelations = [
    'subcategorias'
  ];

  public function grupo()
  {
    return $this->belongsTo(Grupo::class, 'grupo_id');
  }

  public function subcategorias()
  {
    return $this->hasMany(SubCategoria::class, 'categoria_id');
  }

  public function creador()
  {
    return $this->belongsTo(Categoria::class, 'created_by');
  }

}
