<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Cliente;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Traits\DatatableExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{

    use DatatableExport;

    private string $route = 'clientes';

    function __construct()
    {
        $this->middleware("can:{$this->route}.index")->only('index');
        $this->middleware("can:{$this->route}.create")->only('create', 'store');
        $this->middleware("can:{$this->route}.show")->only('show');
        $this->middleware("can:{$this->route}.edit")->only('edit', 'update');
        $this->middleware("can:{$this->route}.destroy")->only('destroy');
        // para los selects
        $this->middleware("can:getSelects")->only('getDepartamentosByPais', 'getCiudadesByDepartamento');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (new Cliente)->getPerPage(); // items por pagina

        $query = Cliente::query();

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
        $clientes = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('cliente.index', compact('clientes', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $cliente = new Cliente();

        $disabled = false;

        $paises = Pais::pluck('nombre', 'id');
        $departamentos = Departamento::pluck('nombre', 'id');
        $ciudades = Ciudad::pluck('nombre', 'id');

        return view('cliente.create', compact('cliente', 'paises', 'departamentos', 'ciudades', 'disabled'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(Cliente::$rules);

        $request['created_by'] = Auth::id();

        // Procesar teléfonos
        $data = $request->all();
        if ($request->has('telefonos') && is_array($request->telefonos)) {
            $data['telefonos'] = array_filter($request->telefonos);
        }

        Cliente::create($data);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Show the form for see the specified resource.
     */
    public function show(int $id): View
    {
        $cliente = Cliente::findOrFail($id);

        $disabled = true;

        $paises = Pais::pluck('nombre', 'id');
        $departamentos = Departamento::where('pais_id', $cliente->pais_id)->pluck('nombre', 'id');
        $ciudades = Ciudad::where('departamento_id', $cliente->departamento_id)->pluck('nombre', 'id');

        return view('cliente.edit', compact('cliente', 'paises', 'departamentos', 'ciudades', 'disabled'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $cliente = Cliente::findOrFail($id);

        $disabled = false;

        $paises = Pais::pluck('nombre', 'id');
        $departamentos = Departamento::where('pais_id', $cliente->pais_id)->pluck('nombre', 'id');
        $ciudades = Ciudad::where('departamento_id', $cliente->departamento_id)->pluck('nombre', 'id');

        return view('cliente.edit', compact('cliente', 'paises', 'departamentos', 'ciudades', 'disabled'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $request->validate(Cliente::rulesForUpdate($cliente->id));

        // Procesar teléfonos
        $data = $request->all();
        if ($request->has('telefonos') && is_array($request->telefonos)) {
            $data['telefonos'] = array_filter($request->telefonos);
        }

        $cliente->update($data);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $cliente = Cliente::findOrFail($id);

        if ($cliente->tieneTransacciones()) {
            return redirect()
                ->route('clientes.index')
                ->with('error', 'Este cliente tiene asignada alguna venta o compra. Si se elimina, afectará la generación de reportes.');
        }

        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }

    /**
     * Obtener departamentos por país
     */
    public function getDepartamentosByPais(int $paisId): \Illuminate\Http\JsonResponse
    {
        $departamentos = Departamento::where('pais_id', $paisId)->get();
        return response()->json($departamentos);
    }

    /**
     * Obtener ciudades por departamento
     */
    public function getCiudadesByDepartamento(int $departamentoId): \Illuminate\Http\JsonResponse
    {
        $ciudades = Ciudad::where('departamento_id', $departamentoId)->get();
        return response()->json($ciudades);
    }
}