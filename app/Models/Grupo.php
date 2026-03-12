<?php

namespace App\Models;

use App\Traits\ProtectRelationships;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
  use ProtectRelationships;

  static $rules = [
    'nombre' => 'required',
  ];

  protected $perPage = 20;

  protected $fillable = [
    'nombre',
    'created_by'
  ];

  protected array $deletionGuardedRelations = [
    'categorias'
  ];

  public function categorias()
  {
    return $this->hasMany(Categoria::class, 'grupo_id');
  }

  public function creador()
  {
    return $this->belongsTo(Categoria::class, 'created_by');
  }
}
