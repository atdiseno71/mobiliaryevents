<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ciudad;
use App\Models\Almacen;
use Illuminate\Http\Request;
use App\Traits\DatatableExport;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Console\View\Components\Factory;

class AlmacenController extends Controller
{

    use DatatableExport;

    private string $route = 'almacenes';

    public function __construct()
    {
        $this->middleware("can:{$this->route}.index")->only('index');
        $this->middleware("can:{$this->route}.create")->only('create', 'store');
        $this->middleware("can:{$this->route}.show")->only('show');
        $this->middleware("can:{$this->route}.edit")->only('edit', 'update');
        $this->middleware("can:{$this->route}.destroy")->only('destroy');
        $this->middleware("can:getSelects")->only('getSelect');
    }

    public function index(Request $request)
    {
        $perPage = (new Almacen)->getPerPage();
        $query = Almacen::with(['ciudad', 'responsable']);

        if ($request->ajax()) {
            $this->dtApplyLength($request, $perPage);

            return DataTables::eloquent($query)
                ->addColumn('ciudad', fn($row) => $row->ciudad?->nombre ?? '-')
                ->addColumn('responsable', fn($row) => $row->responsable?->name ?? '-')
                ->addColumn('action', function ($row) {
                    $route = $this->route;
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        $almacenes = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('almacen.index', compact('almacenes', 'perPage'));
    }

    public function create(): Factory|View
    {
        $data = new Almacen();
        $disabled = false;

        $ciudades = Ciudad::pluck('nombre', 'id');
        $responsables = User::pluck('name', 'id');

        return view('almacen.create', compact('data', 'disabled', 'ciudades', 'responsables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(Almacen::$rules);

        $request['created_by'] = Auth::id();

        Almacen::create($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Creado correctamente.');
    }

    public function show(int $id): View
    {
        $data = Almacen::with(['ciudad', 'responsable'])->findOrFail($id);

        $disabled = true;

        $ciudades = Ciudad::pluck('nombre', 'id');
        $responsables = User::pluck('name', 'id');

        // Usa edit.blade porque tu estándar es el mismo formulario pero deshabilitado
        return view('almacen.edit', compact('data', 'disabled', 'ciudades', 'responsables'));
    }

    public function edit(int $id): Factory|View
    {
        $data = Almacen::findOrFail($id);

        $disabled = false;

        $ciudades = Ciudad::pluck('nombre', 'id');
        $responsables = User::pluck('name', 'id');

        return view('almacen.edit', compact('data', 'disabled', 'ciudades', 'responsables'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate(Almacen::$rules);

        $model = Almacen::findOrFail($id);
        $model->update($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Actualizado correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = Almacen::findOrFail($id);
            $data->delete();

            return redirect()->route("{$this->route}.index")
                ->with('success', 'Eliminado correctamente.');
        } catch (\Throwable $e) {
            return redirect()->route("{$this->route}.index")
                ->with('warning', $e->getMessage());
        }
    }

    public function getSelect(Request $request)
    {
        $data = Almacen::pluck('nombre', 'id');
        return response()->json($data);
    }
}
