<?php

namespace App\Models;

use App\Traits\ProtectRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cotizacion extends Model
{
    use ProtectRelationships, SoftDeletes;

    protected $table = 'cotizaciones';

    public static $rules = [
        'consecutivo' => 'required|string|max:90|unique:cotizaciones,consecutivo',
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

    protected $perPage = 20;

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

    public function getPersonalNombresAttribute(): array
    {
        if (empty($this->personal_ids)) {
            return [];
        }

        if (is_array($this->personal_ids)) {
            $ids = $this->personal_ids;
        } else {
            $decoded = json_decode($this->personal_ids, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $ids = $decoded;
            } else {
                $ids = explode(',', $this->personal_ids);
            }
        }

        return User::whereIn('id', $ids)
            ->pluck('name')
            ->toArray();
    }

    public function getPersonalIdsArrayAttribute(): array
    {
        $value = $this->personal_ids;

        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $json = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            return $json;
        }

        return explode(',', $value);
    }

    public function getTotalAttribute(): float
    {
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

            if (!empty($det->combinacion_id) && $det->combinacion) {
                foreach ($det->combinacion->productos ?? [] as $p) {
                    $total += $qty * $this->precioProducto($p);
                }
                continue;
            }

            if (!empty($det->referencia_id) && $det->referencia) {
                $p = ($det->referencia->productos ?? collect())
                    ->sortByDesc(fn($x) => $this->precioProducto($x))
                    ->first();

                $total += $qty * ($p ? $this->precioProducto($p) : 0);
                continue;
            }

            if (!empty($det->producto_id) && $det->producto) {
                $total += $qty * $this->precioProducto($det->producto);
            }
        }

        return round($total, 2);
    }

    private function precioProducto($p): float
    {
        if (!$p) {
            return 0.0;
        }

        $clase = strtolower(trim((string) ($p->clase ?? '')));

        if ($clase === 'insumo') {
            return (float) ($p->valor_compra ?? 0);
        }

        return (float) ($p->valor_alquiler ?? 0);
    }

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
        return $this->hasMany(CotizacionDetalle::class, 'cotizacion_id');
    }
}