<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';

    protected $fillable = [
        'nombre',
        'pais_id'
    ];

    /**
     * Obtener el país al que pertenece el departamento.
     */
    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class);
    }

    /**
     * Obtener las ciudades del departamento.
     */
    public function ciudades(): HasMany
    {
        return $this->hasMany(Ciudad::class);
    }

    /**
     * Obtener los clientes del departamento.
     */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    /**
     * Scope para búsqueda por país.
     */
    public function scopeByPais($query, $paisId)
    {
        return $query->where('pais_id', $paisId);
    }

    /**
     * Scope para búsqueda rápida.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('nombre', 'like', "%{$search}%")
                    ->orWhereHas('pais', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    });
    }
}