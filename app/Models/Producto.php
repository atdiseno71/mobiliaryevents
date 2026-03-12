<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grupo_id',
        'categoria_id',
        'subcategoria_id',
        'subreferencia_id',
        'marca_id',
        'inventario_por_serie',
        'nombre',
        'descripcion',
        'codigo_qr',
        'valor_compra',
        'valor_alquiler',
        'imagen',
        'clase',
        'peso_unitario',
        'activo',
        'created_by'
    ];

    protected $casts = [
        'inventario_por_serie' => 'boolean',
        'activo' => 'boolean',
        'valor_compra' => 'decimal:2',
        'valor_alquiler' => 'decimal:2',
        'peso_unitario' => 'decimal:2',
    ];

    public static $rules = [
        'grupo_id' => 'nullable|exists:grupos,id',
        'categoria_id' => 'nullable|exists:categorias,id',
        'subcategoria_id' => 'nullable|exists:subcategorias,id',
        'marca_id' => 'nullable|exists:marcas,id',
        'inventario_por_serie' => 'boolean',
        'subreferencia_id' => 'required_if:inventario_por_serie,1|nullable|exists:subreferencias,id',
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'codigo_qr' => 'nullable|string|max:255|unique:productos,codigo_qr',
        'valor_compra' => 'nullable|numeric|min:0',
        'valor_alquiler' => 'nullable|numeric|min:0',
        'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'clase' => 'required|in:Alquiler,Insumo',
        'peso_unitario' => 'nullable|numeric|min:0',
        'activo' => 'boolean'
    ];

    public static function rulesForUpdate($id): array
    {
        return [
            'grupo_id' => 'nullable|exists:grupos,id',
            'categoria_id' => 'nullable|exists:categorias,id',
            'subcategoria_id' => 'nullable|exists:subcategorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'inventario_por_serie' => 'boolean',
            'subreferencia_id' => 'required_if:inventario_por_serie,1|nullable|exists:subreferencias,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'codigo_qr' => 'nullable|string|max:255|unique:productos,codigo_qr,' . $id,
            'valor_compra' => 'nullable|numeric|min:0',
            'valor_alquiler' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'clase' => 'required|in:Alquiler,Insumo',
            'peso_unitario' => 'nullable|numeric|min:0',
            'activo' => 'boolean'
        ];
    }

    protected $appends = [
        'stock_por_almacen',
        'is_clase_insumo'
    ];

    // Relaciones
    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria(): BelongsTo
    {
        return $this->belongsTo(SubCategoria::class);
    }

    public function subreferencia(): BelongsTo
    {
        return $this->belongsTo(SubReferencia::class, 'subreferencia_id');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    public function combinaciones()
    {
        return $this->belongsToMany(
            Combinacion::class,
            'combinaciones_productos',
            'producto_id',
            'combinacion_id'
        )->withTimestamps();
    }

    // Scope de búsqueda
    public function scopeSearch($query, string $term)
    {
        return $query->where('nombre', 'like', "%{$term}%")
            ->orWhereHas('marca', function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%");
            });
    }

    // Accesor para mostrar estado legible
    public function getEstadoTextoAttribute(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    // URL completa de la imagen
    public function getImagenUrlAttribute()
    {
        if (!$this->imagen) {
            return asset('img/sin-imagen.png');
        }

        return asset("storage/productos/$this->imagen");
    }

    // Atributo: stock por almacén
    public function getStockPorAlmacenAttribute()
    {
        return $this->inventarios->mapWithKeys(function ($inv) {
            return [$inv->almacen_id => $inv->stock];
        });
    }

    public function getIsClaseInsumoAttribute(): bool
    {
        $clase = $this->clase ?? null;

        return is_string($clase) && strtolower(trim($clase)) == 'insumo';
    }
}
