<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = [
        'nombre',
        'codigo'
    ];

    /**
     * Obtener los departamentos del país.
     */
    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }

    /**
     * Obtener los clientes del país.
     */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    /**
     * Obtener las ciudades a través de departamentos.
     */
    public function ciudades(): HasMany
    {
        return $this->hasMany(Ciudad::class, Departamento::class);
    }

    /**
     * Scope para búsqueda rápida.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
    }
}