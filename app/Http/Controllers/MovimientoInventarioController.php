<?php

namespace App\Http\Controllers;

use App\Models\Estados;
use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Remision;
use App\Models\Inventario;
use App\Models\Combinacion;
use Illuminate\Http\Request;
use App\Traits\DatatableExport;
use App\Models\MovimientoRemision;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Console\View\Components\Factory;

class MovimientoInventarioController extends Controller
{

    use DatatableExport;

    private string $route = 'movimientos';

    public function __construct()
    {
        $this->middleware("can:{$this->route}.index")->only('index');
        $this->middleware("can:{$this->route}.create")->only('create', 'store');
        $this->middleware("can:{$this->route}.show")->only('show');
        $this->middleware("can:{$this->route}.edit")->only('edit', 'update');
        $this->middleware("can:{$this->route}.destroy")->only('destroy');
        $this->middleware("can:{$this->route}.pdf")->only('pdf');
        $this->middleware("can:getSelects")->only('getSelect');
    }

    public function index(Request $request)
    {
        $perPage = (new MovimientoRemision)->getPerPage();

        $query = MovimientoRemision::with([
            'almacen',
            'remision',
            'user'
        ]);

        if ($request->ajax()) {
            $this->dtApplyLength($request, $perPage);

            return DataTables::eloquent($query)
                ->addColumn('almacen', fn($row) => $row->almacen->nombre ?? '-')
                ->addColumn('remision', fn($row) => $row->remision->consecutivo ?? '-')
                ->addColumn('creado_por', fn($row) => $row->user->name ?? '-')
                ->addColumn('estado', fn($row) => $row->estado->nombre ?? '-')
                ->addColumn('action', function ($row) {
                    $route = $this->route;
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        $movimientos = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('movimientos.index', compact('movimientos', 'perPage'));
    }

    public function create(): Factory|View
    {
        $data = new MovimientoInventario();
        $disabled = false;
        $disabledremision = false;

        $productos = collect(); // vacía a propósito

        $almacenes = Almacen::orderBy('nombre')->pluck('nombre', 'id');
        $remisiones = Remision::where(function ($q) {
            $q->whereNotIn('estado_id', [2, 3])
                ->orWhereNull('estado_id');
        })->orderBy('consecutivo')->pluck('consecutivo', 'id');
        $estados = Estados::orderBy('nombre')->whereIn('id', [1, 2])->pluck('nombre', 'id');

        return view('movimientos.create', compact(
            'productos',
            'almacenes',
            'remisiones',
            'estados',
            'data',
            'disabled',
            'disabledremision'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'almacen_id' => 'required|exists:almacenes,id',
            'remision_id' => 'required|exists:remisiones,id',
            'tipo' => 'required|in:ingreso,salida',
            'productos' => 'required|array|min:1',
            'productos.*' => 'required|integer',
            'inventarios' => 'required|array',
            'cantidades' => 'required|array',
            'cantidades.*' => 'required|integer|min:1',
        ]);

        $remision = Remision::with([
            'detalles.producto',
            'detalles.referencia.productos',
        ])->findOrFail($request->remision_id);

        if ($request->tipo === 'ingreso') {
            $this->ensureSalidaCompletaAntesDeIngreso($remision);
        }

        $this->validateMatchRemisionVsMovimientos($remision, $request->tipo, $request, null);

        // Regla: solo 1 ingreso + 1 salida
        $yaExiste = MovimientoRemision::where('remision_id', $remision->id)
            ->where('tipo', $request->tipo)
            ->exists();

        if ($yaExiste) {
            throw ValidationException::withMessages([
                'tipo' => ["Esta remisión ya tiene un movimiento de tipo '{$request->tipo}'."]
            ]);
        }

        // Validar pertenencia a la remisión
        [$requiredMap] = $this->buildRequiredFromRemision($remision);
        $allowedIds = array_keys($requiredMap);

        foreach ($request->productos as $pid) {
            if (!in_array((int) $pid, $allowedIds, true)) {
                throw ValidationException::withMessages([
                    'productos' => ["El producto {$pid} no pertenece a la remisión."]
                ]);
            }
        }

        $movimientoRemision = null;

        try {

            DB::transaction(function () use ($request, $remision, &$movimientoRemision) {

                $movimientoRemision = MovimientoRemision::create([
                    'almacen_id' => $request->almacen_id,
                    'remision_id' => $remision->id,
                    'tipo' => $request->tipo,
                    'motivo' => $request->motivo,
                    'created_by' => Auth::id(),
                ]);

                foreach ($request->productos as $i => $pid) {

                    $cantidad = (int) ($request->cantidades[$i] ?? 1);
                    $isCombo = $request->inventarios[$i] === 'combo';

                    /*
                    |--------------------------------------------------------------------------
                    | PRODUCTO NORMAL
                    |--------------------------------------------------------------------------
                    */
                    if (!$isCombo) {

                        $inventario = Inventario::where('producto_id', $pid)
                            ->where('almacen_id', $request->almacen_id)
                            ->first();

                        if (!$inventario) {
                            throw new \Exception(
                                "No hay inventario para el producto {$pid} en el almacén {$request->almacen_id}"
                            );
                        }

                        MovimientoInventario::create([
                            'movimientos_remision_id' => $movimientoRemision->id,
                            'inventario_id' => $inventario->id,
                            'producto_id' => $pid,
                            'combinacion_id' => null,
                            'almacen_id' => $request->almacen_id,
                            'remision_id' => $remision->id,
                            'tipo' => $request->tipo,
                            'cantidad' => $cantidad,
                            'motivo' => $request->motivo,
                            'created_by' => Auth::id(),
                        ]);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | COMBO → mover SUS productos
                    |--------------------------------------------------------------------------
                    */
                    $combo = Combinacion::with('productos.inventarios')
                        ->findOrFail($pid);

                    foreach ($combo->productos as $producto) {

                        $inventario = $producto->inventarios
                            ->where('almacen_id', $request->almacen_id)
                            ->first();

                        if (!$inventario) {
                            throw new \Exception(
                                "No hay inventario para el producto {$producto->id} ({$producto->nombre}) en el almacén {$request->almacen_id}"
                            );
                        }

                        MovimientoInventario::create([
                            'movimientos_remision_id' => $movimientoRemision->id,
                            'inventario_id' => $inventario->id,
                            'producto_id' => $producto->id,
                            'combinacion_id' => $combo->id,
                            'almacen_id' => $request->almacen_id,
                            'remision_id' => $remision->id,
                            'tipo' => $request->tipo,
                            'cantidad' => $cantidad,
                            'motivo' => $request->motivo,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            });

            // Recalcular faltantes y estados (igual que tu update)
            $missingSalida = $this->buildMissing($remision, 'salida');
            $missingIngreso = $this->buildMissing($remision, 'ingreso');

            $despachadoId = $this->getEstadoId('EN EVENTO', 4);
            $terminadoId = $this->getEstadoId('FINALIZADO', 2);

            if (empty($missingSalida) && $despachadoId) {
                $remision->update(['estado_id' => $despachadoId]);
            }

            if (empty($missingIngreso) && empty($missingSalida) && $terminadoId) {
                $remision->update(['estado_id' => $terminadoId]);
            }

            $alerts = [];

            if ($request->tipo === 'salida') {
                $alerts = array_filter([
                    $this->formatMissingAlert($missingSalida, 'Faltan por SALIR'),
                ]);
            }

            if ($request->tipo === 'ingreso') {
                $alerts = array_filter([
                    $this->formatMissingAlert($missingIngreso, 'Faltan por ENTRAR'),
                ]);
            }

            if ($movimientoRemision) {
                // Refresca para sincronizar motivo sobre el valor actualizado
                $movimientoRemision->refresh();
                $this->syncMotivoWithAlerts($movimientoRemision, $request->motivo, $alerts);
            }

            $movimientoRemision?->refresh();

            return redirect()
                ->route("{$this->route}.index")
                ->with('success', "Movimiento {$movimientoRemision?->id} registrado correctamente. ({$movimientoRemision?->motivo})");

        } catch (\Throwable $e) {
            return redirect()->route("{$this->route}.index")
                ->with('error', $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $data = MovimientoRemision::with([
            'movimientosinventario.producto.marca',
            'movimientosinventario.producto.subreferencia',
            'movimientosinventario.producto.inventarios',
            'movimientosinventario.combinacion.productos',
            'movimientosinventario.almacen',
            'movimientosinventario.inventario',
            'remision.detalles.producto.grupo',
            'remision.detalles.referencia.productos.grupo',
            'almacen',
            'user'
        ])->findOrFail($id);

        $data->setRelation('detalles', $data->remision->detalles);

        $disabled = false;
        $disabledremision = true;

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS DISPONIBLES PARA EL SELECT
        |--------------------------------------------------------------------------
        */
        $detalles = $data->remision->detalles;

        $productosNormales = $detalles
            ->whereNotNull('producto_id')
            ->pluck('producto_id')
            ->toArray();

        $subrefs = $detalles
            ->whereNotNull('referencia_id')
            ->pluck('referencia_id')
            ->toArray();

        $productos = Producto::with(['marca', 'subreferencia', 'inventarios'])
            ->whereIn('id', $productosNormales)
            ->orWhereIn('subreferencia_id', $subrefs)
            ->orderBy('nombre')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PRELOAD DE PRODUCTOS / COMBOS SELECCIONADOS
        |--------------------------------------------------------------------------
        */
        $preloadedProductos = collect();

        // Productos normales
        $data->movimientosinventario
            ->whereNull('combinacion_id')
            ->each(function ($m) use ($preloadedProductos) {

                if (!$m->producto) {
                    return;
                }

                $preloadedProductos->push([
                    'id' => $m->producto_id,
                    'inv' => $m->producto->inventario_por_serie ? 1 : 0,
                    'cantidad' => $m->cantidad ?? 0,
                    'nombre' => $m->producto->nombre ?? '',
                    'codigo_qr' => $m->producto->codigo_qr ?? '',
                ]);
            });

        // Combos (reconstruidos)
        $data->movimientosinventario
            ->whereNotNull('combinacion_id')
            ->groupBy('combinacion_id')
            ->each(function ($rows, $comboId) use ($preloadedProductos) {

                /** @var \App\Models\MovimientoInventario $first */
                $first = $rows->first();

                $combo = $first->combinacion;

                if (!$combo) {
                    return;
                }

                $preloadedProductos->push([
                    'id' => $combo->id,
                    'inv' => 'combo',
                    'cantidad' => $first->cantidad,
                    'nombre' => $combo->nombre,
                    'codigo_qr' => $combo->codigo_qr ?? '',
                ]);
            });

        /*
        |--------------------------------------------------------------------------
        | SELECTS
        |--------------------------------------------------------------------------
        */
        $almacenes = Almacen::orderBy('nombre')->pluck('nombre', 'id');
        $remisiones = Remision::orderBy('consecutivo')->pluck('consecutivo', 'id');
        $estados = Estados::orderBy('nombre')->whereIn('id', [1, 2])->pluck('nombre', 'id');

        return view('movimientos.edit', compact(
            'data',
            'productos',
            'preloadedProductos',
            'almacenes',
            'remisiones',
            'estados',
            'disabled',
            'disabledremision',
        ));
    }

    public function edit(int $id): View
    {
        $data = MovimientoRemision::with([
            'movimientosinventario.producto.marca',
            'movimientosinventario.producto.subreferencia',
            'movimientosinventario.producto.inventarios',
            'movimientosinventario.combinacion.productos',
            'movimientosinventario.almacen',
            'movimientosinventario.inventario',
            'remision.detalles.producto.grupo',
            'remision.detalles.referencia.productos.grupo',
            'almacen',
            'user'
        ])->findOrFail($id);

        $data->setRelation('detalles', $data->remision->detalles);

        $disabled = false;
        $disabledremision = true;

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS DISPONIBLES PARA EL SELECT
        |--------------------------------------------------------------------------
        */
        $detalles = $data->remision->detalles;

        $productosNormales = $detalles
            ->whereNotNull('producto_id')
            ->pluck('producto_id')
            ->toArray();

        $subrefs = $detalles
            ->whereNotNull('referencia_id')
            ->pluck('referencia_id')
            ->toArray();

        $productos = Producto::with(['marca', 'subreferencia', 'inventarios'])
            ->whereIn('id', $productosNormales)
            ->orWhereIn('subreferencia_id', $subrefs)
            ->orderBy('nombre')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PRELOAD DE PRODUCTOS / COMBOS SELECCIONADOS
        |--------------------------------------------------------------------------
        */
        $preloadedProductos = collect();

        // 1️⃣ Productos normales
        $data->movimientosinventario
            ->whereNull('combinacion_id')
            ->each(function ($m) use ($preloadedProductos) {

                if (!$m->producto) {
                    return;
                }

                $preloadedProductos->push([
                    'id' => $m->producto_id,
                    'inv' => $m->producto->inventario_por_serie ? 1 : 0,
                    'cantidad' => $m->cantidad ?? 0,
                    'nombre' => $m->producto->nombre ?? '',
                    'codigo_qr' => $m->producto->codigo_qr ?? '',
                    'is_insumo' => $m->producto->is_clase_insumo,
                ]);
            });

        // 2️⃣ Combos (reconstruidos)
        $data->movimientosinventario
            ->whereNotNull('combinacion_id')
            ->groupBy('combinacion_id')
            ->each(function ($rows, $comboId) use ($preloadedProductos) {

                /** @var \App\Models\MovimientoInventario $first */
                $first = $rows->first();

                $combo = $first->combinacion;

                if (!$combo) {
                    return;
                }

                $preloadedProductos->push([
                    'id' => $combo->id,
                    'inv' => 'combo',
                    'cantidad' => $first->cantidad,
                    'nombre' => $combo->nombre,
                    'codigo_qr' => $combo->codigo_qr ?? '',
                    'is_insumo' => false,
                ]);
            });

        /*
        |--------------------------------------------------------------------------
        | SELECTS
        |--------------------------------------------------------------------------
        */
        $almacenes = Almacen::orderBy('nombre')->pluck('nombre', 'id');
        $remisiones = Remision::orderBy('consecutivo')->pluck('consecutivo', 'id');
        $estados = Estados::orderBy('nombre')->whereIn('id', [1, 2])->pluck('nombre', 'id');

        return view('movimientos.edit', compact(
            'data',
            'productos',
            'preloadedProductos',
            'almacenes',
            'remisiones',
            'estados',
            'disabled',
            'disabledremision',
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'almacen_id' => 'required|exists:almacenes,id',
            'remision_id' => 'required|exists:remisiones,id',
            'tipo' => 'required|in:ingreso,salida',
            'productos' => 'required|array|min:1',
            'productos.*' => 'required|integer',
            'inventarios' => 'required|array',
            'cantidades' => 'required|array',
            'cantidades.*' => 'required|integer|min:1',
        ]);

        $mov = MovimientoRemision::with('remision')->findOrFail($id);

        // No permitir cambiar remisión en edición (tal cual tu lógica)
        if ((int) $request->remision_id !== (int) $mov->remision_id) {
            return back()->withInput()->with('mov_alerts', [
                'No puedes cambiar la remisión en edición. Crea un movimiento nuevo.'
            ]);
        }

        $remision = Remision::with([
            'detalles.producto',
            'detalles.referencia.productos',
            // si ya estás usando combinaciones en buildRequiredFromRemision, esto es consistente
            'detalles.combinacion.productos',
        ])->findOrFail($mov->remision_id);

        if ($request->tipo === 'ingreso') {
            $this->ensureSalidaCompletaAntesDeIngreso($remision);
        }

        $this->validateMatchRemisionVsMovimientos($remision, $request->tipo, $request, $mov->id);

        // Regla: solo 1 ingreso + 1 salida (excepto este movimiento)
        $yaExiste = MovimientoRemision::where('remision_id', $remision->id)
            ->where('tipo', $request->tipo)
            ->where('id', '!=', $mov->id)
            ->exists();

        if ($yaExiste) {
            throw ValidationException::withMessages([
                'tipo' => ["Esta remisión ya tiene un movimiento de tipo '{$request->tipo}'."]
            ]);
        }

        // Validar pertenencia a la remisión (igual que store)
        [$requiredMap] = $this->buildRequiredFromRemision($remision);
        $allowedIds = array_keys($requiredMap);

        foreach ($request->productos as $pid) {
            if (!in_array((int) $pid, $allowedIds, true)) {
                throw ValidationException::withMessages([
                    'productos' => ["El producto {$pid} no pertenece a la remisión."]
                ]);
            }
        }

        try {

            DB::transaction(function () use ($request, $mov, $remision) {

                // Igual que store: actualizar lo propio del movimiento (sin inventar columnas)
                $mov->update([
                    'almacen_id' => $request->almacen_id,
                    'tipo' => $request->tipo,
                    'motivo' => $request->motivo,
                ]);

                // Borrar detalle anterior y recrear (igual enfoque que tu update actual, pero con combos)
                MovimientoInventario::where('movimientos_remision_id', $mov->id)->delete();

                foreach ($request->productos as $i => $pid) {

                    $cantidad = (int) ($request->cantidades[$i] ?? 1);
                    $isCombo = ($request->inventarios[$i] ?? null) === 'combo';

                    /*
                    |----------------------------------------------------------------------
                    | PRODUCTO NORMAL
                    |----------------------------------------------------------------------
                    */
                    if (!$isCombo) {

                        $inventario = Inventario::where('producto_id', $pid)
                            ->where('almacen_id', $request->almacen_id)
                            ->first();

                        if (!$inventario) {
                            throw new \Exception(
                                "No hay inventario para el producto {$pid} en el almacén {$request->almacen_id}"
                            );
                        }

                        MovimientoInventario::create([
                            'movimientos_remision_id' => $mov->id,
                            'inventario_id' => $inventario->id,
                            'producto_id' => $pid,
                            'combinacion_id' => null,
                            'almacen_id' => $request->almacen_id,
                            'remision_id' => $remision->id,
                            'tipo' => $request->tipo,
                            'cantidad' => $cantidad,
                            'motivo' => $request->motivo,
                            'created_by' => Auth::id(),
                        ]);

                        continue;
                    }

                    /*
                    |----------------------------------------------------------------------
                    | COMBO → mover SUS productos (igual que store)
                    |----------------------------------------------------------------------
                    */
                    $combo = Combinacion::with('productos.inventarios')->findOrFail($pid);

                    foreach ($combo->productos as $producto) {

                        $inventario = $producto->inventarios
                            ->where('almacen_id', $request->almacen_id)
                            ->first();

                        if (!$inventario) {
                            throw new \Exception(
                                "No hay inventario para el producto {$producto->id} ({$producto->nombre}) en el almacén {$request->almacen_id}"
                            );
                        }

                        MovimientoInventario::create([
                            'movimientos_remision_id' => $mov->id,
                            'inventario_id' => $inventario->id,
                            'producto_id' => $producto->id,
                            'combinacion_id' => $combo->id,
                            'almacen_id' => $request->almacen_id,
                            'remision_id' => $remision->id,
                            'tipo' => $request->tipo,
                            'cantidad' => $cantidad,
                            'motivo' => $request->motivo,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            });

            // Recalcular faltantes y estados (igual que tu update)
            $missingSalida = $this->buildMissing($remision, 'salida');
            $missingIngreso = $this->buildMissing($remision, 'ingreso');

            $despachadoId = $this->getEstadoId('EN EVENTO', 4);
            $terminadoId = $this->getEstadoId('FINALIZADO', 2);

            if (empty($missingSalida) && $despachadoId) {
                $remision->update(['estado_id' => $despachadoId]);
            }

            if (empty($missingIngreso) && empty($missingSalida) && $terminadoId) {
                $remision->update(['estado_id' => $terminadoId]);
            }

            $alerts = [];

            if ($request->tipo === 'salida') {
                $alerts = array_filter([
                    $this->formatMissingAlert($missingSalida, 'Faltan por SALIR'),
                ]);
            }

            if ($request->tipo === 'ingreso') {
                $alerts = array_filter([
                    $this->formatMissingAlert($missingIngreso, 'Faltan por ENTRAR'),
                ]);
            }

            // Refresca para sincronizar motivo sobre el valor actualizado
            $mov->refresh();
            $this->syncMotivoWithAlerts($mov, $request->motivo, $alerts);
            $mov->refresh();

            $redirect = redirect()->route("{$this->route}.index")
                ->with('success', "Movimiento {$mov->id} actualizado correctamente. ({$mov->motivo})");

            if (!empty($alerts)) {
                $redirect->with('mov_alerts', $alerts);
            }

            return $redirect;

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return redirect()->route("{$this->route}.index")
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = MovimientoRemision::findOrFail($id);
            $data->delete();

            return redirect()->route("{$this->route}.index")
                ->with('success', 'Movimiento eliminado correctamente.');
        } catch (\Throwable $e) {
            return redirect()->route("{$this->route}.index")
                ->with('warning', $e->getMessage());
        }
    }

    public function getSelect(Request $request)
    {
        $data = Remision::pluck('consecutivo', 'id');
        return response()->json($data);
    }

    public function pdf($id)
    {
        $remision = Remision::with([
            'cliente',
            'detalles.producto',
            'detalles.referencia'
        ])->findOrFail($id);

        $pdf = \PDF::loadView('remisiones.pdf', compact('remision'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("remision-{$remision->consecutivo}.pdf");
    }

    private function getEstadoId(string $needle, ?int $fallback = null): ?int
    {
        $id = Estados::whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($needle) . '%'])->value('id');
        return $id ?? $fallback;
    }

    /**
     * Devuelve [requiredMap, nombresMap]
     * requiredMap: [producto_id => cantidad_requerida]
     */
    /**
     * Devuelve [allowedMap, nombresMap]
     * allowedMap: [id_seleccionable => 1]  (productos normales + combos + productos de referencias)
     */
    private function buildRequiredFromRemision(Remision $remision): array
    {
        $remision->loadMissing([
            'detalles.producto.grupo',
            'detalles.referencia.productos.grupo',
            'detalles.combinacion.productos.grupo',
        ]);

        $allowed = [];

        foreach ($remision->detalles as $det) {

            // Producto normal (seleccionable por producto_id)
            if ($det->producto_id) {
                $pid = (int) $det->producto_id;
                $allowed[$pid] = 1;
                continue;
            }

            // Combo (seleccionable por combinacion_id)
            if ($det->combinacion_id && $det->combinacion) {
                $cid = (int) $det->combinacion_id;
                $allowed[$cid] = 1;
                continue;
            }

            // Referencia: en UI terminas seleccionando productos, no la referencia
            if ($det->referencia_id && $det->referencia && $det->referencia->productos) {
                $ids = $det->referencia->productos->pluck('id');
                foreach ($ids as $pid) {
                    $allowed[(int) $pid] = 1;
                }
            }
        }

        // (Este nombresMap aquí no es crítico para tu validación; lo dejamos sin inventar combos)
        $nombres = Producto::whereIn('id', array_keys($allowed))
            ->pluck('nombre', 'id')
            ->toArray();

        return [$allowed, $nombres];
    }

    /**
     * Devuelve [requiredProducts, nombresProducts]
     * requiredProducts: [producto_id => cantidad_requerida]
     * Aquí los combos se expanden a sus productos.
     */
    private function buildRequiredProductsForMissing(Remision $remision): array
    {
        $remision->loadMissing([
            'detalles.producto.grupo',
            'detalles.referencia.productos.grupo',
            'detalles.combinacion.productos.grupo',
        ]);

        $required = [];

        foreach ($remision->detalles as $det) {

            $cantidad = (int) ($det->cantidad ?? 1);

            // Producto normal
            if ($det->producto_id) {
                $pid = (int) $det->producto_id;
                $required[$pid] = ($required[$pid] ?? 0) + $cantidad;
                continue;
            }

            // Combo -> sumar SUS productos
            if ($det->combinacion_id && $det->combinacion) {
                foreach ($det->combinacion->productos as $producto) {
                    $pid = (int) $producto->id;
                    $required[$pid] = ($required[$pid] ?? 0) + $cantidad;
                }
                continue;
            }

            // Referencia (tu lógica original)
            if ($det->referencia_id && $det->referencia && $det->referencia->productos) {

                $take = (int) ($det->cantidad ?? $det->referencia->productos->count());

                $ids = $det->referencia->productos
                    ->pluck('id')
                    ->take($take);

                foreach ($ids as $pid) {
                    $pid = (int) $pid;
                    $required[$pid] = ($required[$pid] ?? 0) + 1;
                }
            }
        }

        $nombres = Producto::whereIn('id', array_keys($required))
            ->pluck('nombre', 'id')
            ->toArray();

        return [$required, $nombres];
    }

    /**
     * Devuelve faltantes para un tipo ('salida'|'ingreso'):
     * [ [producto_id, nombre, falta], ... ]
     */
    private function buildMissing(Remision $remision, string $tipo): array
    {
        [$required, $nombres] = $this->buildRequiredProductsForMissing($remision);

        $moved = MovimientoInventario::where('remision_id', $remision->id)
            ->where('tipo', $tipo)
            ->selectRaw('producto_id, SUM(cantidad) as qty')
            ->whereNotNull('producto_id')
            ->groupBy('producto_id')
            ->pluck('qty', 'producto_id')
            ->toArray();

        $missing = [];

        foreach ($required as $pid => $reqQty) {
            $done = (int) ($moved[$pid] ?? 0);
            $faltan = max(0, (int) $reqQty - $done);

            if ($faltan > 0) {
                $missing[] = [
                    'producto_id' => $pid,
                    'nombre' => $nombres[$pid] ?? ("Producto #{$pid}"),
                    'falta' => $faltan,
                ];
            }
        }

        return $missing;
    }

    /**
     * Agrupa por nombre y suma faltantes, para que no repita:
     * AMPLIFICADOR X (2), AMPLIFICADOR X (3)  ->  AMPLIFICADOR X (5)
     */
    private function formatMissingAlert(array $missing, string $label): ?string
    {
        if (empty($missing))
            return null;

        $items = collect($missing)
            ->groupBy('nombre')
            ->map(function ($rows, $nombre) {
                $sum = collect($rows)->sum('falta');
                return "{$nombre} ({$sum})";
            })
            ->values()
            ->implode(', ');

        return "{$label}: {$items}";
    }

    /**
     * ============================
     *  NO DUPLICAR EN MOTIVO
     * ============================
     * Guarda un bloque automático “AUTO_MOV” dentro de motivo.
     * Cada vez que se guarda, primero borra el bloque viejo y escribe el nuevo.
     */
    private function autoTagStart(): string
    {
        return '[AUTO_MOV]';
    }

    private function autoTagEnd(): string
    {
        return '[/AUTO_MOV]';
    }

    private function stripAutoBlock(string $text): string
    {
        if ($text === '') {
            return '';
        }

        // Normaliza caracteres invisibles (por si vienen mezclados)
        $text = str_replace(["\u{200B}", "\u{200C}", "\u{FEFF}"], '', $text);

        // Elimina CUALQUIER bloque AUTO_MOV existente (uno o muchos)
        $text = preg_replace(
            '/\[AUTO_MOV\].*?\[\/AUTO_MOV\]\s*/is',
            '',
            $text
        ) ?? $text;

        return trim($text);
    }

    private function buildAutoBlock(array $alerts): string
    {
        if (empty($alerts)) {
            return '';
        }

        return $this->autoTagStart()
            . "\n" . implode("\n", $alerts) . "\n"
            . $this->autoTagEnd();
    }

    /**
     * Sincroniza motivo con:
     * - lo que escribió el usuario (request->motivo)
     * - + bloque auto (faltantes)
     *
     * Importante: nunca duplica, siempre reemplaza el bloque auto.
     */
    private function syncMotivoWithAlerts(
        MovimientoRemision $mov,
        ?string $motivoFromRequest,
        array $alerts
    ): void {
        // Fuente de verdad: lo que YA está guardado
        $base = (string) ($mov->motivo ?? '');

        // Si viene algo del request, úsalo (pero limpio)
        if ($motivoFromRequest !== null) {
            $base = $motivoFromRequest;
        }

        // Limpia cualquier AUTO_MOV previo (uno o muchos)
        $base = $this->stripAutoBlock($base);

        // Si no hay nada pendiente, agrega mensaje de match completo
        if (empty($alerts)) {
            $tipo = strtoupper((string) ($mov->tipo ?? ''));
            if ($tipo !== '') {
                $alerts = ["EL MATCH SE HA COMPLETADO, {$tipo} DE LOS ITEMS SE HA COMPLETADO"];
                $mov->update(['estado_id' => 2]);
            } else {
                $mov->update(['estado_id' => 1]);
            }
        }

        $auto = $this->buildAutoBlock($alerts);

        $nuevo = trim($base . ($auto ? "\n\n" . $auto : ''));

        if (trim((string) $mov->motivo) !== $nuevo) {
            $mov->update(['motivo' => $nuevo]);
        }
    }

    /**
     * Mapa requerido por PRODUCTO real para un tipo de movimiento.
     * Expande combos a sus productos.
     * required: [producto_id => cantidad_requerida]
     */
    private function buildRequiredProductsForMatch(Remision $remision): array
    {
        $remision->loadMissing([
            'detalles.producto',
            'detalles.referencia.productos',
            'detalles.combinacion.productos',
        ]);

        $required = [];

        foreach ($remision->detalles as $det) {
            $cantidad = (int) ($det->cantidad ?? 1);

            // Producto normal
            if ($det->producto_id) {
                $pid = (int) $det->producto_id;
                $required[$pid] = ($required[$pid] ?? 0) + $cantidad;
                continue;
            }

            // Combo -> sus productos
            if ($det->combinacion_id && $det->combinacion) {
                foreach ($det->combinacion->productos as $p) {
                    $pid = (int) $p->id;
                    $required[$pid] = ($required[$pid] ?? 0) + $cantidad;
                }
                continue;
            }

            // Referencia -> tu regla original (take)
            if ($det->referencia_id && $det->referencia && $det->referencia->productos) {
                $take = (int) ($det->cantidad ?? $det->referencia->productos->count());
                $ids = $det->referencia->productos->pluck('id')->take($take);

                foreach ($ids as $pid) {
                    $pid = (int) $pid;
                    $required[$pid] = ($required[$pid] ?? 0) + 1;
                }
            }
        }

        return $required;
    }

    /**
     * Mapa ya movido por PRODUCTO real, para una remisión y tipo.
     * excludeMovRemisionId: para update (no contarse a sí mismo)
     */
    private function buildAlreadyMovedMap(int $remisionId, string $tipo, ?int $excludeMovRemisionId = null): array
    {
        $q = MovimientoInventario::where('remision_id', $remisionId)
            ->where('tipo', $tipo)
            ->whereNotNull('producto_id');

        if ($excludeMovRemisionId) {
            $q->where('movimientos_remision_id', '!=', $excludeMovRemisionId);
        }

        return $q->selectRaw('producto_id, SUM(cantidad) as qty')
            ->groupBy('producto_id')
            ->pluck('qty', 'producto_id')
            ->toArray();
    }

    /**
     * Convierte la selección del usuario (productos + combos) a:
     * attempt: [producto_id => cantidad_total_que_quiere_mover]
     */
    private function buildAttemptMapFromRequest(Request $request): array
    {
        $attempt = [];

        foreach ($request->productos as $i => $pid) {

            $cantidad = (int) ($request->cantidades[$i] ?? 1);
            $isCombo = ($request->inventarios[$i] ?? null) === 'combo';

            // Producto normal
            if (!$isCombo) {
                $p = (int) $pid;
                $attempt[$p] = ($attempt[$p] ?? 0) + $cantidad;
                continue;
            }

            // Combo -> sumar a cada producto interno
            $combo = Combinacion::with('productos:id')->findOrFail($pid);

            foreach ($combo->productos as $p) {
                $prodId = (int) $p->id;
                $attempt[$prodId] = ($attempt[$prodId] ?? 0) + $cantidad;
            }
        }

        return $attempt;
    }

    /**
     * Valida que el intento no supere lo requerido por remisión,
     * considerando lo ya movido (y excluyendo el movimiento actual si aplica).
     */
    private function validateMatchRemisionVsMovimientos(
        Remision $remision,
        string $tipo,
        Request $request,
        ?int $excludeMovRemisionId = null
    ): void {
        $required = $this->buildRequiredProductsForMatch($remision);
        $already = $this->buildAlreadyMovedMap($remision->id, $tipo, $excludeMovRemisionId);
        $attempt = $this->buildAttemptMapFromRequest($request);

        $errors = [];

        foreach ($attempt as $productoId => $qtyIntento) {

            $req = (int) ($required[$productoId] ?? 0);
            $done = (int) ($already[$productoId] ?? 0);

            $nombre = Producto::where('id', $productoId)->value('nombre') ?? "Producto #{$productoId}";

            // si no pertenece a la remisión, req será 0 -> explota aquí (súper bien)
            if ($req <= 0) {
                $errors['productos'][] = "Estas agregando más productos de los que se requieren para la remisión, {$nombre} ID: {$productoId}.";
                continue;
            }

            $maxDisponible = max(0, $req - $done);

            if ($qtyIntento > $maxDisponible) {
                $errors['cantidades'][] =
                    // "{$nombre}: intentas agregar {$qtyIntento}, pero solo puedes agregar {$maxDisponible} (remisión {$req}, ya agregados {$done}).";
                    "{$nombre}: intentas agregar {$qtyIntento}, pero solo puedes agregar {$maxDisponible} (En la remisión solo se requiere {$req}).";
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function ensureSalidaCompletaAntesDeIngreso(Remision $remision): void
    {
        $missingSalida = $this->buildMissing($remision, 'salida');

        if (!empty($missingSalida)) {
            $msg = $this->formatMissingAlert($missingSalida, 'No puedes registrar INGRESO. Faltan por SALIR');
            throw ValidationException::withMessages([
                'tipo' => [$msg ?? 'No puedes registrar INGRESO hasta completar la SALIDA.'],
            ]);
        }
    }

}
