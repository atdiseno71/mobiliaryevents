<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grupo;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Estados;
use App\Models\Producto;
use App\Models\TipoEvento;
use App\Models\Combinacion;
use Illuminate\Http\Request;
use App\Models\Cotizacion;
use App\Traits\DatatableExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\CotizacionDetalle;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\View\Factory;

class CotizacionController extends Controller
{
    use DatatableExport;

    private string $route = 'cotizaciones';

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
        $perPage = (new Cotizacion())->getPerPage();

        $query = Cotizacion::query()->with([
            'cliente',
            'tipoEvento',
            'ciudad',
            'estado',
            'creador',
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

        $cotizaciones = $query->orderByDesc('id')->paginate($perPage);

        return view('cotizaciones.index', compact('cotizaciones', 'perPage'));
    }

    public function create(Request $request): Factory|View
    {
        $disabled = false;
        $cloneId = $request->query('clone_id');

        if ($cloneId) {
            $source = Cotizacion::with([
                'detalles.producto',
                'detalles.referencia',
                'detalles.combinacion',
            ])->findOrFail($cloneId);

            $data = $source->replicate();
            $data->consecutivo = null;
            $data->created_by = null;
            $data->setRelation('detalles', $source->detalles);
        } else {
            $data = new Cotizacion();
        }

        $next = (Cotizacion::max('id') ?? 0) + 1;
        $data->consecutivo = str_pad((string) $next, 5, '0', STR_PAD_LEFT);

        $cotizacionesParaClonar = Cotizacion::orderByDesc('id')
            ->limit(200)
            ->pluck('consecutivo', 'id');

        $clonar_cotizacion = false;

        return view('cotizaciones.create', array_merge(
            $this->getCotizacionFormData($data, true),
            compact('disabled', 'clonar_cotizacion', 'cotizacionesParaClonar')
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(Cotizacion::$rules);

        $request['created_by'] = Auth::id();
        $request['personal_ids'] = $request->personal_ids ?? [];
        $request['estado_id'] = $request->estado_id ?? 1;

        DB::transaction(function () use ($request, &$data) {
            $data = Cotizacion::create(
                $request->except('productos', 'cantidades', 'inventarios', 'is_insumos', 'is_combos')
            );

            $this->syncDetalles($data, $request);
        });

        return redirect()
            ->route("{$this->route}.index")
            ->with('success', 'Cotización creada correctamente.');
    }

    public function show(int $id): Factory|View
    {
        $data = Cotizacion::with([
            'detalles',
            'detalles.producto.inventarios',
            'detalles.referencia.productos',
            'detalles.combinacion.productos',
        ])->findOrFail($id);

        $disabled = true;
        $clonar_cotizacion = true;

        return view('cotizaciones.edit', array_merge(
            $this->getCotizacionFormData($data, true),
            compact('disabled', 'clonar_cotizacion')
        ));
    }

    public function edit(int $id): Factory|View
    {
        $data = Cotizacion::with([
            'detalles',
            'detalles.producto.inventarios',
            'detalles.referencia.productos',
            'detalles.combinacion.productos',
        ])->findOrFail($id);

        $disabled = false;
        $clonar_cotizacion = true;

        return view('cotizaciones.edit', array_merge(
            $this->getCotizacionFormData($data, true),
            compact('disabled', 'clonar_cotizacion')
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $rules = Cotizacion::$rules;
        $rules['consecutivo'] = "required|string|max:90|unique:cotizaciones,consecutivo,{$id}";

        $request['personal_ids'] = $request->personal_ids ?? [];
        $request->validate($rules);

        $model = Cotizacion::findOrFail($id);

        DB::transaction(function () use ($request, $model) {
            $model->update(
                $request->except('productos', 'cantidades', 'inventarios', 'is_insumos', 'is_combos')
            );

            $this->syncDetalles($model, $request);
        });

        return redirect()
            ->route("{$this->route}.index")
            ->with('success', 'Cotización actualizada correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $data = Cotizacion::findOrFail($id);

            if (in_array($data->estado_id, [3], true)) {
                $data->delete();

                return redirect()
                    ->route("{$this->route}.index")
                    ->with('success', 'Cotización eliminada correctamente.');
            }

            return redirect()
                ->route("{$this->route}.index")
                ->with('error', 'Solo se pueden eliminar cotizaciones canceladas.');
        } catch (\Throwable $e) {
            return redirect()
                ->route("{$this->route}.index")
                ->with('error', $e->getMessage());
        }
    }

    public function getSelect(Request $request)
    {
        $data = Cotizacion::pluck('consecutivo', 'id');
        return response()->json($data);
    }

    public function pdf(int $id)
    {
        $cotizacion = Cotizacion::with([
            'cliente',
            'ciudad',
            'tipoEvento',
            'detalles',
            'detalles.producto',
            'detalles.producto.grupo',
            'detalles.referencia',
            'detalles.referencia.productos',
            'detalles.referencia.productos.grupo',
            'detalles.combinacion',
            'detalles.combinacion.productos',
            'detalles.combinacion.productos.grupo',
        ])->findOrFail($id);

        $pdf = \PDF::loadView('cotizaciones.pdf', compact('cotizacion'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("cotizacion-{$cotizacion->consecutivo}.pdf");
    }

    public function getCotizacion(Request $request, int $id)
    {
        $cotizacion = Cotizacion::with([
            'detalles.producto.grupo',
            'detalles.referencia.productos.grupo',
            'detalles.combinacion',
            'detalles.combinacion.productos.grupo',
        ])->findOrFail($id);

        return response()->json($cotizacion);
    }

    private function getCotizacionFormData(?Cotizacion $data = null, bool $includeCombos = true): array
    {
        $data ??= new Cotizacion();

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
        $isSerie = !$isCombo && (((int) $inventarioRaw === 1) || ($inventarioRaw === 'subreferencia'));
        $isInsumo = in_array(($request->is_insumos[$i] ?? 0), [1, '1'], true);

        $cantidad = (int) ($request->cantidades[$i] ?? 1);
        if ($cantidad <= 0) {
            $cantidad = 1;
        }

        $combinacionId = $isCombo ? $valor : null;
        $productoId = (!$isCombo && !$isSerie && ($isProductoSinSerie || $isInsumo)) ? $valor : null;
        $referenciaId = (!$isCombo && $isSerie && !$isProductoSinSerie && !$isInsumo) ? $valor : null;

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

    private function syncDetalles(Cotizacion $cotizacion, Request $request): void
    {
        CotizacionDetalle::where('cotizacion_id', $cotizacion->id)->delete();

        $productos = $request->input('productos', []);

        foreach ($productos as $i => $valor) {
            $detalle = $this->normalizeDetalleFromRequest($request, (int) $i, (int) $valor);

            CotizacionDetalle::create(array_merge($detalle, [
                'cotizacion_id' => $cotizacion->id,
            ]));
        }
    }
}