<?php

namespace App\Models;

use App\Traits\ProtectRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remision extends Model
{
    use ProtectRelationships, SoftDeletes;

    protected $table = 'remisiones';

    /**
     * Validation rules
     */
    public static $rules = [
        'consecutivo' => 'required|string|max:90|unique:remisiones,consecutivo',
        'contacto' => 'nullable|string|max:90',

        'cliente_id' => 'nullable|exists:clientes,id',
        'tipo_evento_id' => 'nullable|exists:tipo_eventos,id',
        'ciudad_id' => 'nullable|exists:ciudades,id',

        'lugar' => 'nullable|string|max:120',
        'personal_ids' => 'nullable|array',
        'transporte' => 'nullable|string|max:60',
        'placa' => 'nullable|string|max:20',
        'id_conductor' => 'nullable|string|max:30',
        'origen' => 'nullable|string|max:120',
        'destino' => 'nullable|string|max:120',

        'estado_id' => 'nullable|exists:estados,id',
        'created_by' => 'nullable|exists:users,id',
    ];

    /**
     * Pagination size
     */
    protected $perPage = 20;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'consecutivo',
        'contacto',

        'cliente_id',
        'tipo_evento_id',
        'ciudad_id',

        'lugar',
        'fecha_evento',
        'fecha_montaje',
        'personal_ids',
        'transporte',
        'placa',
        'id_conductor',
        'origen',
        'destino',

        'estado_id',
        'created_by',
    ];

    protected $casts = [
        'personal_ids' => 'array',
    ];

    protected $appends = [
        'total',
    ];

    protected array $deletionGuardedRelations = [
        // 'detalles',
    ];

    // atributos
    public function getPersonalNombresAttribute()
    {
        if (empty($this->personal_ids)) {
            return [];
        }

        // Si viene como array JSON → ya es array
        if (is_array($this->personal_ids)) {
            $ids = $this->personal_ids;
        } else {
            // Si viene como string → convertirlo a array seguro
            $decoded = json_decode($this->personal_ids, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $ids = $decoded;
            } else {
                // fallback: formato viejo "1,2,3"
                $ids = explode(',', $this->personal_ids);
            }
        }

        return \App\Models\User::whereIn('id', $ids)
            ->pluck('name')
            ->toArray();
    }

    public function getPersonalIdsArrayAttribute()
    {
        $value = $this->personal_ids;

        if (empty($value)) {
            return [];
        }

        // Si ya viene como array
        if (is_array($value)) {
            return $value;
        }

        // Intentar decodificar JSON
        $json = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            return $json;
        }

        // Formato viejo: "1,2,3"
        return explode(',', $value);
    }

    /**
     * TOTAL de la remisión (sumatoria de detalles)
     */
    public function getTotalAttribute(): float
    {
        // Si no hay detalles, 0
        if (!$this->relationLoaded('detalles')) {
            $this->load([
                'detalles.producto:id,clase,valor_compra,valor_alquiler',
                'detalles.combinacion.productos:id,clase,valor_compra,valor_alquiler',
                'detalles.referencia.productos:id,subreferencia_id,clase,valor_compra,valor_alquiler',
            ]);
        }

        $total = 0.0;

        foreach ($this->detalles as $det) {
            $qty = max(1, (int) ($det->cantidad ?? 1));

            // COMBO: suma todos los productos del combo * cantidad del detalle
            if (!empty($det->combinacion_id) && $det->combinacion) {
                foreach ($det->combinacion->productos ?? [] as $p) {
                    $total += $qty * $this->precioProducto($p);
                }
                continue;
            }

            // REFERENCIA: toma el más caro (según regla de precio) y lo multiplica por cantidad
            if (!empty($det->referencia_id) && $det->referencia) {
                $p = ($det->referencia->productos ?? collect())
                    ->sortByDesc(fn($x) => $this->precioProducto($x))
                    ->first();

                $total += $qty * ($p ? $this->precioProducto($p) : 0);
                continue;
            }

            // PRODUCTO
            if (!empty($det->producto_id) && $det->producto) {
                $total += $qty * $this->precioProducto($det->producto);
            }
        }

        return round($total, 2);
    }

    private function precioProducto($p): float
    {
        if (!$p)
            return 0.0;

        $clase = strtolower(trim((string) ($p->clase ?? '')));

        // Regla: Insumo usa compra, Alquiler usa alquiler
        if ($clase === 'insumo') {
            return (float) ($p->valor_compra ?? 0);
        }

        return (float) ($p->valor_alquiler ?? 0);
    }

    /**
     * Calcula el total (en centavos) de un detalle:
     * - combo: suma todos los productos del combo
     * - referencia: toma el producto más caro de la referencia
     * - producto: toma su valor
     */
    private function detalleTotalCents(RemisionDetalle $det): int
    {
        $qty = (int) ($det->cantidad ?? 1);
        if ($qty <= 0)
            $qty = 1;

        // 1) COMBO
        if (!empty($det->combinacion_id)) {
            $combo = $det->combinacion;

            if (!$combo || !$combo->relationLoaded('productos') || $combo->productos->isEmpty()) {
                return 0;
            }

            $comboUnitCents = 0;
            foreach ($combo->productos as $p) {
                $comboUnitCents += $this->productoUnitCents($p);
            }

            return $comboUnitCents * $qty;
        }

        // 2) REFERENCIA
        if (!empty($det->referencia_id)) {
            $ref = $det->referencia;

            if (!$ref || !$ref->relationLoaded('productos') || $ref->productos->isEmpty()) {
                return 0;
            }

            // Tomar el producto más caro de la referencia (para no subestimar)
            $maxUnitCents = 0;
            foreach ($ref->productos as $p) {
                $maxUnitCents = max($maxUnitCents, $this->productoUnitCents($p));
            }

            return $maxUnitCents * $qty;
        }

        // 3) PRODUCTO NORMAL
        if (!empty($det->producto_id)) {
            return $this->productoUnitCents($det->producto) * $qty;
        }

        return 0;
    }

    /**
     * Precio unitario (en centavos) según reglas:
     * - Si clase == 'Insumo' => valor_compra
     * - Si no => valor_alquiler
     * - Si no hay valor => 0
     */
    private function productoUnitCents(?Producto $p): int
    {
        if (!$p)
            return 0;

        $clase = is_string($p->clase) ? strtolower(trim($p->clase)) : '';
        $isInsumo = ($clase === 'insumo');

        $value = $isInsumo ? ($p->valor_compra ?? null) : ($p->valor_alquiler ?? null);

        // fallback: si el valor principal viene null, intenta el otro
        if ($value === null) {
            $value = $isInsumo ? ($p->valor_alquiler ?? null) : ($p->valor_compra ?? null);
        }

        return $this->moneyToCents($value);
    }

    /**
     * Convierte a centavos soportando string|float|int|null (decimal:2 suele venir como string)
     */
    private function moneyToCents($value): int
    {
        if ($value === null || $value === '')
            return 0;

        // Normaliza coma decimal si llega así
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }

        $float = (float) $value;
        if (!is_finite($float) || $float <= 0)
            return 0;

        return (int) round($float * 100);
    }

    /**
     * ================
     *   RELACIONES
     * ================
     */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function tipoEvento()
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function detalles()
    {
        return $this->hasMany(RemisionDetalle::class, 'remision_id');
    }

}
