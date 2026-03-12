<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Models\Ciudad;
use Illuminate\View\View;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Traits\DatatableExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class ProveedorController extends Controller
{

    use DatatableExport;

    private string $route = 'proveedores';

    function __construct()
    {
        $this->middleware("can:{$this->route}.index")->only('index');
        $this->middleware("can:{$this->route}.create")->only('create', 'store');
        $this->middleware("can:{$this->route}.show")->only('show');
        $this->middleware("can:{$this->route}.edit")->only('edit', 'update');
        $this->middleware("can:{$this->route}.destroy")->only('destroy');
        // para los selects
        $this->middleware("can:getSelects")->only('getSelect');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (new Proveedor)->getPerPage(); // items por pagina

        $query = Proveedor::query();

        if ($request->ajax()) {
            $this->dtApplyLength($request, $perPage); // aseguramos la paginacion

            return DataTables::eloquent($query)
                ->addColumn('action', function ($row) {
                    $route = $this->route;
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['action']) // Permitir HTML sin escape en la columna "action"
                ->toJson();
        }

        // si no ordenaron ordena acá
        $proveedores = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('proveedor.index', compact('proveedores', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $proveedor = new Proveedor();

        $disabled = false;

        $paises = Pais::pluck('nombre', 'id');
        $departamentos = Departamento::pluck('nombre', 'id');
        $ciudades = Ciudad::pluck('nombre', 'id');

        return view('proveedor.create', compact('proveedor', 'paises', 'departamentos', 'ciudades', 'disabled'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(Proveedor::$rules);

        $request['created_by'] = Auth::id();

        // Procesar teléfonos
        $data = $request->all();
        if ($request->has('telefonos') && is_array($request->telefonos)) {
            $data['telefonos'] = array_filter($request->telefonos);
        }

        Proveedor::create($data);

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Show the form for see the specified resource.
     */
    public function show(int $id): View
    {
        $proveedor = Proveedor::findOrFail($id);

        $disabled = true;

        $paises = Pais::pluck('nombre', 'id');
        $departamentos = Departamento::where('pais_id', $proveedor->pais_id)->pluck('nombre', 'id');
        $ciudades = Ciudad::where('departamento_id', $proveedor->departamento_id)->pluck('nombre', 'id');

        return view('proveedor.edit', compact('proveedor', 'paises', 'departamentos', 'ciudades', 'disabled'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $proveedor = Proveedor::findOrFail($id);

        $disabled = false;

        $paises = Pais::pluck('nombre', 'id');
        $departamentos = Departamento::where('pais_id', $proveedor->pais_id)->pluck('nombre', 'id');
        $ciudades = Ciudad::where('departamento_id', $proveedor->departamento_id)->pluck('nombre', 'id');

        return view('proveedor.edit', compact('proveedor', 'paises', 'departamentos', 'ciudades', 'disabled'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate(Proveedor::rulesForUpdate($id));

        // Procesar teléfonos
        $data = $request->all();
        if ($request->has('telefonos') && is_array($request->telefonos)) {
            $data['telefonos'] = array_filter($request->telefonos);
        }

        $proveedor = Proveedor::findOrFail($id);

        $proveedor->update($data);

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $proveedor = Proveedor::findOrFail($id);

        if ($proveedor->tieneTransacciones()) {
            return redirect()
                ->route('proveedores.index')
                ->with('error', 'Este proveedor tiene asignada alguna venta o compra. Si se elimina, afectará la generación de reportes.');
        }

        $proveedor->delete();

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }

    /**
     * Obtener select por país
     */
    public function getSelect(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = Proveedor::pluck('nombre', 'id');
        return response()->json($data);
    }
}