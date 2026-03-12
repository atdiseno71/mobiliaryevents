<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ciudad extends Model
{
    use HasFactory;

    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
        'departamento_id'
    ];

    /**
     * Obtener el departamento al que pertenece la ciudad.
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Obtener el país a través del departamento.
     */
    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_id', 'id', 'departamento.pais');
    }

    /**
     * Obtener los clientes de la ciudad.
     */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    /**
     * Scope para búsqueda por departamento.
     */
    public function scopeByDepartamento($query, $departamentoId)
    {
        return $query->where('departamento_id', $departamentoId);
    }

    /**
     * Scope para búsqueda rápida.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('nombre', 'like', "%{$search}%")
                    ->orWhereHas('departamento', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                          ->orWhereHas('pais', function ($q2) use ($search) {
                              $q2->where('nombre', 'like', "%{$search}%");
                          });
                    });
    }

    /**
     * Accesor para obtener el nombre completo (ciudad, departamento, país).
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre}, {$this->departamento->nombre}, {$this->departamento->pais->nombre}";
    }

    /**
     * Accesor para obtener el ID del país.
     */
    public function getPaisIdAttribute(): int
    {
        return $this->departamento->pais_id;
    }
}