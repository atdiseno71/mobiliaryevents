<?php

namespace App\Models;

use App\Traits\ProtectRelationships;
use Illuminate\Database\Eloquent\Model;

class Combinacion extends Model
{
  use ProtectRelationships;

  protected $table = 'combinaciones';

  static $rules = [
    'nombre' => 'required',
    'codigo_qr' => 'required',
  ];

  protected $perPage = 20;

  protected $fillable = [
    'nombre',
    'codigo_qr',
    'created_by'
  ];

  protected array $deletionGuardedRelations = [
    'productos'
  ];

  protected $appends = [
    'stock_por_almacen',
  ];

  public function productos()
  {
    return $this->belongsToMany(
      Producto::class,
      'combinaciones_productos',
      'combinacion_id',
      'producto_id'
    )->withTimestamps();
  }


  // Atributo: stock por almacén
  public function getStockPorAlmacenAttribute()
  {
    $stocksPorAlmacen = [];

    // recorrer cada producto del combo
    foreach ($this->productos as $producto) {

      foreach ($producto->inventarios as $inventario) {
        $almacenId = $inventario->almacen_id;
        $stockProd = (int) $inventario->stock;

        // si aún no existe, inicializar
        if (!isset($stocksPorAlmacen[$almacenId])) {
          $stocksPorAlmacen[$almacenId] = $stockProd;
        } else {
          // tomar el mínimo (regla del combo)
          $stocksPorAlmacen[$almacenId] = min(
            $stocksPorAlmacen[$almacenId],
            $stockProd
          );
        }
      }
    }

    return $stocksPorAlmacen;
  }

  public function creador()
  {
    return $this->belongsTo(User::class, 'created_by');
  }
}
