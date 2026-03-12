<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategoria extends Model
{

  protected $table = 'subcategorias';

  static $rules = [
    'nombre' => 'required',
    'categoria_id' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = [
    'nombre',
    'categoria_id',
    'created_by'
  ];

  public function categoria()
  {
    return $this->belongsTo(Categoria::class, 'categoria_id');
  }

  public function creador()
  {
    return $this->belongsTo(Categoria::class, 'created_by');
  }

}
