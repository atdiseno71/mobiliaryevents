<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * Reglas de validación
     */
    public static $rules = [
        'codigo' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255|unique:users,email',
        'password' => 'required|string|min:6',
        'pais_id' => 'required|exists:paises,id',
        'departamento_id' => 'required|exists:departamentos,id',
        'ciudad_id' => 'required|exists:ciudades,id',
        'direccion' => 'required|string|max:255',
        'telefono_fijo' => 'nullable|string|max:255',
        'telefono_movil' => 'nullable|string|max:255',
        'tipo_identificacion' => 'nullable|string|max:255',
        'identificacion' => 'nullable|string|max:255',
        'nivel' => 'required|exists:roles,id',
        'estado' => 'required|in:Activo,Inactivo',
    ];

    /**
     * Atributos que pueden ser asignados en masa.
     */
    protected $fillable = [
        'codigo',
        'tipo_identificacion',
        'identificacion',
        'name',
        'email',
        'pais_id',
        'departamento_id',
        'ciudad_id',
        'direccion',
        'telefono_fijo',
        'telefono_movil',
        'nivel',
        'estado',
        'foto',
        'password',
    ];

    /**
     * Atributos ocultos al serializar el modelo.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos que deben tener conversión de tipo.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'nivel_original',
    ];

    public function getNivelAttribute()
    {
        // Si el usuario tiene algún rol, devolvemos el primero
        return $this->getRoleNames()->first() ?? 'Sin rol';
    }

    public function getNivelOriginalAttribute()
    {
        return $this->attributes['nivel'] ?? null;
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

    public function adminlte_image()
    {
        if (!empty($this->foto)) {
            $path = ltrim($this->foto, '/');

            if (Storage::disk('public')->exists($path)) {
                return Storage::url($path);
            }
        }

        return asset('img/user.png');
    }

    public function adminlte_desc()
    {
        return $this->name;
    }

}
