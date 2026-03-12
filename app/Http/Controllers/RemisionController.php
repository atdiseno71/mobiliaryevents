<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grupo;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Estados;
use App\Models\Producto;
use App\Models\Remision;
use App\Models\TipoEvento;
use App\Models\Combinacion;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use App\Models\RemisionDetalle;
use App\Traits\DatatableExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Console\View\Components\Factory;

class RemisionController extends Controller
{

    use DatatableExport;

    private string $route = 'remisiones';

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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (new Remision)->getPerPage();

        $query = Remision::query()->with([
            'cliente',
            'tipoEvento',
            'ciudad',
            'estado',
            'creador',

            // para el total
            'detalles',
            'detalles.producto:id,clase,valor_compra,valor_alquiler',
            'detalles.combinacion:id',
            'detalles.combinacion.productos:id,clase,valor_compra,valor_alquiler',
            'detalles.referencia:id',
            'detalles.referencia.productos:id,subreferencia_id,clase,valor_compra,valor_alquiler',
        ]);

        if ($request->ajax()) {
            $this->dtApplyLength($request, $perPage);

            return DataTables::eloquent($query)
                ->addColumn('total_calculado', fn($row) => number_format((float) $row->total, 2, ',', '.'))
                ->addColumn('cliente', fn($row) => $row->cliente->nombre ?? '-')
                ->addColumn('tipo_evento', fn($row) => $row->tipoEvento->nombre ?? '-')
                ->addColumn('estado', fn($row) => $row->estado->nombre ?? '-')
                ->addColumn('action', function ($row) {
                    $route = $this->route;
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        $remisiones = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('remisiones.index', compact('remisiones', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */

    // public function create(Request $request): Factory|View
    // {
    //     $disabled = false;

    //     $cloneId = $request->query('clone_id');

    //     if ($cloneId) {
    //         $source = Remision::with([
    //             'detalles.producto',
    //             'detalles.referencia',
    //             'detalles.combinacion',
    //         ])->findOrFail($cloneId);

    //         $data = $source->replicate();
    //         $data->consecutivo = null;
    //         $data->created_by = null;
    //         $data->setRelation('detalles', $source->detalles);
    //     } else {
    //         $data = new Remision();
    //     }

    //     // Productos
    //     $productos = Producto::with(['marca', 'subreferencia'])
    //         ->withSum('inventarios as stock_total', 'stock')
    //         ->orderBy('nombre')
    //         ->get();

    //     // COMBOS (como referencia)
    //     $combos = Combinacion::with([
    //         'productos.grupo'
    //     ])->orderBy('nombre')->get();

    //     // Listados
    //     $clientes = Cliente::orderBy('nombre')->pluck('nombre', 'id');
    //     $tipoEventos = TipoEvento::orderBy('nombre')->pluck('nombre', 'id');
    //     $ciudades = Ciudad::orderBy('nombre')->pluck('nombre', 'id');
    //     $estados = Estados::orderBy('nombre')->pluck('nombre', 'id');
    //     $users = User::orderBy('name')->pluck('name', 'id');
    //     $grupos = Grupo::orderBy('id', 'asc')->pluck('nombre', 'id');

    //     $remisionesParaClonar = Remision::orderByDesc('id')
    //         ->limit(200)
    //         ->pluck('consecutivo', 'id');

    //     $clonar_remision = false;

    //     return view('remisiones.create', compact(
    //         'productos',
    //         'combos',
    //         'clientes',
    //         'tipoEventos',
    //         'ciudades',
    //         'estados',
    //         'users',
    //         'grupos',
    //         'remisionesParaClonar',
    //         'data',
    //         'disabled',
    //         'clonar_remision'
    //     ));
    // }
    public function create(Request $request): Factory|View
    {
        $disabled = false;
        $cloneId = $request->query('clone_id');

        if ($cloneId) {
            $source = Remision::with(['detalles.producto', 'detalles.referencia', 'detalles.combinacion'])
                ->findOrFail($cloneId);

            $data = $source->replicate();
            $data->consecutivo = null;
            $data->created_by = null;
            $data->setRelation('detalles', $source->detalles);
        } else {
            $data = new Remision();
        }

        // consecutivo sugerido
        $next = (Remision::max('id') ?? 0) + 1;
        $data->consecutivo = str_pad((string) $next, 5, '0', STR_PAD_LEFT);

        $remisionesParaClonar = Remision::orderByDesc('id')->limit(200)->pluck('consecutivo', 'id');
        $clonar_remision = false;

        return view('remisiones.create', array_merge(
            $this->getRemisionFormData($data, true),
            compact('disabled', 'clonar_remision', 'remisionesParaClonar')
        ));
    }

    /**
     * Store a newly created resource.
     */
    // public function store(Request $request): RedirectResponse
    // {
    //     // dd($request->all());
    //     $request->validate(Remision::$rules);

    //     $request['created_by'] = Auth::id();
    //     $request['personal_ids'] = $request->personal_ids ?? [];
    //     $request['estado_id'] = $request->estado_id ?? 1;

    //     DB::transaction(function () use ($request, &$data) {

    //         $data = Remision::create(
    //             $request->except('productos', 'cantidades', 'inventarios', 'is_insumos', 'is_combos')
    //         );

    //         $productos = $request->input('productos', []);
    //         foreach ($productos as $i => $valor) {

    //             $inventarioRaw = (string) ($request->inventarios[$i] ?? '0');

    //             $isCombo = ($inventarioRaw === 'combo') || ((int) ($request->is_combos[$i] ?? 0) === 1);

    //             $isProductoSinSerie = in_array($inventarioRaw, ['producto_sin_serie', '0'], true);

    //             $isSerie = (!$isCombo) && (
    //                 ((int) $inventarioRaw === 1) || ($inventarioRaw === 'subreferencia')
    //             );

    //             $isInsumo = in_array(($request->is_insumos[$i] ?? 0), [1, '1'], true);

    //             $cantidad = (int) ($request->cantidades[$i] ?? 1);
    //             if ($cantidad <= 0)
    //                 $cantidad = 1;

    //             $combinacionId = $isCombo ? $valor : null;
    //             $productoId = (!$isCombo && !$isSerie && ($isProductoSinSerie || $isInsumo)) ? $valor : null;
    //             $referenciaId = (!$isCombo && $isSerie && !$isProductoSinSerie && !$isInsumo) ? $valor : null;

    //             // fallback: si ninguno aplica, por defecto producto
    //             \Log::info([
    //                 'combinacionId' => $combinacionId,
    //                 'productoId' => $productoId,
    //                 'referenciaId' => $referenciaId,
    //             ]);
    //             if ($combinacionId === null && $productoId === null && $referenciaId === null) {
    //                 $productoId = $valor;
    //             }

    //             RemisionDetalle::create([
    //                 'remision_id' => $data->id,
    //                 'combinacion_id' => $combinacionId,
    //                 'producto_id' => $productoId,
    //                 'referencia_id' => $referenciaId,
    //                 'cantidad' => $cantidad,
    //             ]);
    //         }
    //     });

    //     return redirect()
    //         ->route("{$this->route}.index")
    //         ->with('success', 'Remisión creada correctamente.');
    // }
    public function store(Request $request): RedirectResponse
    {
        $request->validate(Remision::$rules);

        $request['created_by'] = Auth::id();
        $request['personal_ids'] = $request->personal_ids ?? [];
        $request['estado_id'] = $request->estado_id ?? 1;

        DB::transaction(function () use ($request, &$data) {
            $data = Remision::create($request->except('productos', 'cantidades', 'inventarios', 'is_insumos', 'is_combos'));
            $this->syncDetalles($data, $request);
        });

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Remisión creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): Factory|View
    {
        $data = Remision::with([
            'detalles',
            'detalles.producto.inventarios',
            'detalles.referencia.productos',
            'detalles.combinacion.productos',
        ])->findOrFail($id);

        $disabled = true;
        $clonar_remision = true;

        return view('remisiones.edit', array_merge(
            $this->getRemisionFormData($data, true),
            compact('disabled', 'clonar_remision')
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(int $id): Factory|View
    // {
    //     // Cargar remisión + detalles completos (producto / referencia / combo)
    //     $data = Remision::with([
    //         'detalles',
    //         'detalles.producto',
    //         'detalles.producto.inventarios',
    //         'detalles.referencia',
    //         'detalles.referencia.productos',
    //         'detalles.combinacion',
    //         'detalles.combinacion.productos',
    //     ])->findOrFail($id);

    //     $disabled = false;

    //     // Productos (para el listado de la izquierda)
    //     $productos = Producto::with(['marca', 'subreferencia'])
    //         ->withSum('inventarios as stock_total', 'stock')
    //         ->orderBy('nombre')
    //         ->get();

    //     // Combos (para el bloque de combos arriba)
    //     $combos = Combinacion::with(['productos.grupo'])
    //         ->orderBy('nombre')
    //         ->get();

    //     // Listados del formulario
    //     $clientes = Cliente::orderBy('nombre')->pluck('nombre', 'id');
    //     $tipoEventos = TipoEvento::orderBy('nombre')->pluck('nombre', 'id');
    //     $ciudades = Ciudad::orderBy('nombre')->pluck('nombre', 'id');
    //     $estados = Estados::orderBy('nombre')->pluck('nombre', 'id');
    //     $users = User::orderBy('name')->pluck('name', 'id');
    //     $grupos = Grupo::orderBy('id', 'asc')->pluck('nombre', 'id');

    //     $clonar_remision = true;

    //     return view('remisiones.edit', compact(
    //         'productos',
    //         'combos',
    //         'clientes',
    //         'tipoEventos',
    //         'ciudades',
    //         'estados',
    //         'users',
    //         'grupos',
    //         'data',
    //         'disabled',
    //         'clonar_remision'
    //     ));
    // }


    public function edit(int $id): Factory|View
    {
        $data = Remision::with([
            'detalles',
            'detalles.producto.inventarios',
            'detalles.referencia.productos',
            'detalles.combinacion.productos',
        ])->findOrFail($id);

        $disabled = false;
        $clonar_remision = true;

        return view('remisiones.edit', array_merge(
            $this->getRemisionFormData($data, true),
            compact('disabled', 'clonar_remision')
        ));
    }

    /**
     * Update the specified resource.
     */
    // public function update(Request $request, int $id)
    // {
    //     // dd($request->all());
    //     $rules = Remision::$rules;
    //     $rules['consecutivo'] = "required|string|max:90|unique:remisiones,consecutivo,$id";

    //     $request['personal_ids'] = $request->personal_ids ?? [];
    //     $request->validate($rules);

    //     $model = Remision::findOrFail($id);

    //     DB::transaction(function () use ($request, $model) {

    //         $model->update(
    //             $request->except('productos', 'cantidades', 'inventarios', 'is_insumos', 'is_combos')
    //         );

    //         RemisionDetalle::where('remision_id', $model->id)->delete();

    //         $productos = $request->input('productos', []);
    //         foreach ($productos as $i => $valor) {

    //             $inventarioRaw = (string) ($request->inventarios[$i] ?? '0'); // "0" | "1" | "combo" | "producto_sin_serie" | "subreferencia
    //             $isCombo = in_array($inventarioRaw, ['combo'], true) || ((int) ($request->is_combos[$i] ?? 0) === 1);
    //             $isProductoSinSerie = in_array($inventarioRaw, ['producto_sin_serie', 0, '0'], true);
    //             // "1" | "subreferencia"
    //             $isSerie = (!$isCombo) && (((int) $inventarioRaw === 1) || in_array($inventarioRaw, ['subreferencia'], true));
    //             // si es insumo
    //             $isInsumo = in_array($request->is_insumos[$i], [1, '1'], true);

    //             $cantidad = (int) ($request->cantidades[$i] ?? 1);
    //             if ($cantidad <= 0)
    //                 $cantidad = 1;

    //             RemisionDetalle::create([
    //                 'remision_id' => $model->id,
    //                 'combinacion_id' => $isCombo ? $valor : null,
    //                 'producto_id' => (!$isCombo && !$isSerie && ($isProductoSinSerie || $isInsumo)) ? $valor : null,
    //                 'referencia_id' => (!$isCombo && $isSerie && !$isProductoSinSerie && !$isInsumo) ? $valor : null,
    //                 'cantidad' => $cantidad,
    //             ]);
    //         }
    //     });

    //     return redirect()
    //         ->route("{$this->route}.index")
    //         ->with('success', 'Remisión actualizada correctamente.');
    // }

    public function update(Request $request, int $id): RedirectResponse
    {
        $rules = Remision::$rules;
        $rules['consecutivo'] = "required|string|max:90|unique:remisiones,consecutivo,$id";

        $request['personal_ids'] = $request->personal_ids ?? [];
        $request->validate($rules);

        $model = Remision::findOrFail($id);

        DB::transaction(function () use ($request, $model) {
            $model->update($request->except('productos', 'cantidades', 'inventarios', 'is_insumos', 'is_combos'));
            $this->syncDetalles($model, $request);
        });

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Remisión actualizada correctamente.');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $data = Remision::findOrFail($id);

            // solo eliminar cancelados
            if (in_array($data->estado_id, [3])) {
                $data->delete();
                return redirect()->route("{$this->route}.index")
                    ->with('success', 'Remisión eliminada correctamente.');
            }

            return redirect()->route("{$this->route}.index")
                ->with('error', 'Solo se pueden eliminar remisiones canceladas.');
        } catch (\Throwable $e) {
            return redirect()->route("{$this->route}.index")
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Support for selects Ajax.
     */
    public function getSelect(Request $request)
    {
        $data = Remision::pluck('consecutivo', 'id');
        return response()->json($data);
    }

    public function pdf($id)
    {
        $remision = Remision::with([
            'cliente',
            'ciudad',
            'tipoEvento',

            // Detalles
            'detalles',

            // Producto -> grupo
            'detalles.producto',
            'detalles.producto.grupo',

            // Referencia -> productos -> grupo
            'detalles.referencia',
            'detalles.referencia.productos',
            'detalles.referencia.productos.grupo',

            // Combo -> productos -> grupo
            'detalles.combinacion',
            'detalles.combinacion.productos',
            'detalles.combinacion.productos.grupo',
        ])->findOrFail($id);

        $pdf = \PDF::loadView('remisiones.pdf', compact('remision'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("remision-{$remision->consecutivo}.pdf");
    }

    // public function getRemision(Request $request, $id)
    // {
    //     $remision = Remision::with([
    //         'detalles.producto.grupo',
    //         'detalles.referencia.productos.grupo',
    //         'detalles.combinacion',
    //         'detalles.combinacion.productos.grupo',
    //     ])->findOrFail($id);

    //     return response()->json($remision);
    // }
    public function getRemision(Request $request, $id)
    {
        $tipo = $request->input('tipo', 'salida'); // salida | ingreso

        $remision = Remision::with([
            'detalles.producto.grupo',
            'detalles.referencia.productos.grupo',
            'detalles.combinacion',
            'detalles.combinacion.productos.grupo',
        ])->findOrFail($id);

        // Si NO es ingreso, devuelves normal
        if ($tipo !== 'ingreso') {
            return response()->json($remision);
        }

        // ===========================
        // INGRESO => SOLO lo que YA SALIÓ
        // ===========================
        $salidosIds = MovimientoInventario::where('remision_id', $remision->id)
            ->where('tipo', 'salida')
            ->whereNotNull('producto_id')
            ->distinct()
            ->pluck('producto_id')
            ->map(fn($v) => (int) $v)
            ->toArray();

        // Si no ha salido nada, no hay nada por ingresar
        if (empty($salidosIds)) {
            $remision->setRelation('detalles', collect());
            return response()->json($remision);
        }

        $detallesFiltrados = $remision->detalles->map(function ($det) use ($salidosIds) {

            // 1) Producto normal
            if (!empty($det->producto_id)) {
                return in_array((int) $det->producto_id, $salidosIds, true) ? $det : null;
            }

            // 2) Referencia -> filtra sus productos
            if (!empty($det->referencia_id) && $det->referencia && $det->referencia->productos) {
                $det->referencia->setRelation(
                    'productos',
                    $det->referencia->productos->whereIn('id', $salidosIds)->values()
                );

                return $det->referencia->productos->isNotEmpty() ? $det : null;
            }

            // 3) Combo -> se incluye si alguno de sus productos salió
            if (!empty($det->combinacion_id) && $det->combinacion && $det->combinacion->productos) {
                $comboIds = $det->combinacion->productos->pluck('id')->map(fn($v) => (int) $v)->all();
                $tieneAlguno = count(array_intersect($comboIds, $salidosIds)) > 0;

                return $tieneAlguno ? $det : null;
            }

            return null;

        })->filter()->values();

        $remision->setRelation('detalles', $detallesFiltrados);

        return response()->json($remision);
    }

    private function getRemisionFormData(?Remision $data = null, bool $includeCombos = true): array
    {
        $data ??= new Remision();

        $productos = Producto::with(['marca', 'subreferencia'])
            ->withSum('inventarios as stock_total', 'stock')
            ->orderBy('nombre')
            ->get();

        $combos = $includeCombos
            ? Combinacion::with(['productos.grupo'])->orderBy('nombre')->get()
            : collect();

        return [
            'data' => $data,
            'productos' => $productos,
            'combos' => $combos,

            'clientes' => Cliente::orderBy('nombre')->pluck('nombre', 'id'),
            'tipoEventos' => TipoEvento::orderBy('nombre')->pluck('nombre', 'id'),
            'ciudades' => Ciudad::orderBy('nombre')->pluck('nombre', 'id'),
            'estados' => Estados::orderBy('nombre')->pluck('nombre', 'id'),
            'users' => User::orderBy('name')->pluck('name', 'id'),
            'grupos' => Grupo::orderBy('id', 'asc')->pluck('nombre', 'id'),
        ];
    }

    private function normalizeDetalleFromRequest(Request $request, int $i, int $valor): array
    {
        $inventarioRaw = (string) ($request->inventarios[$i] ?? '0');

        $isCombo = ($inventarioRaw === 'combo') || ((int) ($request->is_combos[$i] ?? 0) === 1);

        $isProductoSinSerie = in_array($inventarioRaw, ['producto_sin_serie', '0'], true);

        $isSerie = (!$isCombo) && (
            ((int) $inventarioRaw === 1) || ($inventarioRaw === 'subreferencia')
        );

        $isInsumo = in_array(($request->is_insumos[$i] ?? 0), [1, '1'], true);

        $cantidad = (int) ($request->cantidades[$i] ?? 1);
        if ($cantidad <= 0)
            $cantidad = 1;

        $combinacionId = $isCombo ? $valor : null;
        $productoId = (!$isCombo && !$isSerie && ($isProductoSinSerie || $isInsumo)) ? $valor : null;
        $referenciaId = (!$isCombo && $isSerie && !$isProductoSinSerie && !$isInsumo) ? $valor : null;

        // fallback: si no cayó en ninguna regla, que sea producto
        if ($combinacionId === null && $productoId === null && $referenciaId === null) {
            $productoId = $valor;
        }

        return [
            'combinacion_id' => $combinacionId,
            'producto_id' => $productoId,
            'referencia_id' => $referenciaId,
            'cantidad' => $cantidad,
        ];
    }

    private function syncDetalles(Remision $remision, Request $request): void
    {
        RemisionDetalle::where('remision_id', $remision->id)->delete();

        $productos = $request->input('productos', []);

        foreach ($productos as $i => $valor) {
            $detalle = $this->normalizeDetalleFromRequest($request, (int) $i, (int) $valor);

            RemisionDetalle::create(array_merge($detalle, [
                'remision_id' => $remision->id,
            ]));
        }
    }

}
