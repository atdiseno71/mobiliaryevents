<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'proveedores';

  protected $fillable = [
    'nombre',
    'email',
    'telefonos',
    'direccion',
    'pais_id',
    'departamento_id',
    'ciudad_id'
  ];

  protected $casts = [
    'telefonos' => 'array',
  ];

  /**
   * Reglas de validación para crear un cliente
   */
  public static $rules = [
    'nombre' => 'required|string|max:255',
    'email' => 'nullable|email|unique:clientes,email',
    'telefonos' => 'nullable|string|max:20',
    'direccion' => 'nullable|string|max:500',
    'pais_id' => 'required|exists:paises,id',
    'departamento_id' => 'required|exists:departamentos,id',
    'ciudad_id' => 'required|exists:ciudades,id',
  ];

  /**
   * Reglas de validación para actualizar un cliente (ignorando el email actual)
   */
  public static function rulesForUpdate($clienteId): array
  {
    return [
      'nombre' => 'required|string|max:255',
      'email' => 'nullable|email|unique:clientes,email,' . $clienteId,
      'telefonos' => 'nullable|array',
      'telefonos.*' => 'nullable|string|max:20',
      'direccion' => 'nullable|string|max:500',
      'pais_id' => 'required|exists:paises,id',
      'departamento_id' => 'required|exists:departamentos,id',
      'ciudad_id' => 'required|exists:ciudades,id',
    ];
  }

  public function pais(): BelongsTo
  {
    return $this->belongsTo(Pais::class);
  }

  public function departamento(): BelongsTo
  {
    return $this->belongsTo(Departamento::class);
  }

  public function ciudad(): BelongsTo
  {
    return $this->belongsTo(Ciudad::class);
  }

  /**
   * Obtener las ventas del cliente
   */
  public function ventas(): HasMany
  {
    return $this->hasMany(Venta::class, 'id_comprador');
  }

  /**
   * Obtener las compras del cliente
   */
  public function compras(): HasMany
  {
    return $this->hasMany(Compra::class, 'id_comprador');
  }

  /**
   * Accesor para obtener el primer teléfono
   */
  public function getTelefonoPrincipalAttribute(): ?string
  {
    if (is_array($this->telefonos) && !empty($this->telefonos)) {
      // Filtrar valores nulos o vacíos y obtener el primero
      $telefonosValidos = array_filter($this->telefonos, function ($telefono) {
        return !empty($telefono);
      });
      return reset($telefonosValidos) ?: null;
    }
    return null;
  }

  /**
   * Accesor para obtener la ubicación completa
   */
  public function getUbicacionCompletaAttribute(): string
  {
    $partes = [];
    if ($this->ciudad)
      $partes[] = $this->ciudad->nombre;
    if ($this->departamento)
      $partes[] = $this->departamento->nombre;
    if ($this->pais)
      $partes[] = $this->pais->nombre;

    return implode(', ', $partes);
  }

  /**
   * Scope para búsqueda de clientes
   */
  public function scopeSearch($query, string $search)
  {
    return $query->where('nombre', 'like', "%{$search}%")
      ->orWhere('email', 'like', "%{$search}%")
      ->orWhere('direccion', 'like', "%{$search}%")
      ->orWhereHas('pais', function ($q) use ($search) {
        $q->where('nombre', 'like', "%{$search}%");
      })
      ->orWhereHas('departamento', function ($q) use ($search) {
        $q->where('nombre', 'like', "%{$search}%");
      })
      ->orWhereHas('ciudad', function ($q) use ($search) {
        $q->where('nombre', 'like', "%{$search}%");
      });
  }

  /**
   * Verificar si el cliente tiene transacciones
   */
  public function tieneTransacciones(): bool
  {
    return $this->ventas()->exists() || $this->compras()->exists();
  }
}