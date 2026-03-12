<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Producto;
use App\Models\Combinacion;
use Illuminate\Http\Request;
use App\Traits\DatatableExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Console\View\Components\Factory;

class CombinacionController extends Controller
{

    use DatatableExport;

    private string $route = 'combinaciones';

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
        $perPage = (new Combinacion)->getPerPage();
        $query = Combinacion::query();

        if ($request->ajax()) {
            $this->dtApplyLength($request, $perPage);

            return DataTables::eloquent($query)
                ->addColumn('items', function ($row) {
                    return $row->productos->count();
                })
                ->addColumn('action', function ($row) {
                    $route = $this->route;
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        $grupos = $query->orderBy('id', 'desc')->paginate($perPage);
        return view('combinaciones.index', compact('grupos', 'perPage'));
    }

    public function create(): Factory|View
    {
        $data = new Combinacion();
        $disabled = false;

        // todos los productos (el frontend puede filtrar, pero el backend manda)
        $productos = Producto::orderBy('nombre')->whereDoesntHave('combinaciones')->get(['id', 'nombre', 'codigo_qr', 'grupo_id']);
        $grupos = Grupo::orderBy('id', 'asc')->pluck('nombre', 'id');
        $preselected = collect();

        return view('combinaciones.create', compact(
            'data',
            'grupos',
            'disabled',
            'productos',
            'preselected'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(array_merge(Combinacion::$rules, [
            'productos' => 'required|array',
            'productos.*' => 'integer|distinct|exists:productos,id',
        ]));

        $ids = array_values(array_unique($request->input('productos', [])));

        // validar que no estén usados en otra combinación
        if (!empty($ids)) {
            $usados = DB::table('combinaciones_productos')
                ->whereIn('producto_id', $ids)
                ->exists();

            if ($usados) {
                throw ValidationException::withMessages([
                    'productos' => 'Uno o más productos ya están asignados a otra combinación.',
                ]);
            }
        }

        $data = Combinacion::create([
            'nombre' => $request->nombre,
            'codigo_qr' => $request->codigo_qr,
            'created_by' => Auth::id(),
        ]);

        $data->productos()->sync($ids);

        return redirect()
            ->route("{$this->route}.index")
            ->with('success', 'Creado correctamente.');
    }

    public function show(int $id): View
    {
        $data = Combinacion::with('productos')->findOrFail($id);

        $preselected = $data->productos->map(fn($p) => [
            'id' => $p->id,
            'nombre' => $p->nombre ?? "Producto #{$p->id}",
            'codigo_qr' => $p->codigo_qr ?? '',
            'grupo_id' => $p->grupo_id ?? '',
        ]);

        $disabled = true;
        $productos = Producto::orderBy('nombre')->get(['id', 'nombre', 'codigo_qr']);
        $grupos = Grupo::orderBy('id', 'asc')->pluck('nombre', 'id');

        return view('combinaciones.edit', compact(
            'data',
            'grupos',
            'disabled',
            'productos',
            'preselected'
        ));
    }

    public function edit(int $id): Factory|View
    {
        $data = Combinacion::with('productos')->findOrFail($id);

        $selectedIds = $data->productos->pluck('id')->toArray();
        $preselected = $data->productos->map(fn($p) => [
            'id' => $p->id,
            'nombre' => $p->nombre ?? "Producto #{$p->id}",
            'codigo_qr' => $p->codigo_qr ?? '',
            'grupo_id' => $p->grupo_id ?? '',
        ]);

        $disabled = false;
        $productos = Producto::orderBy('nombre')->where(function ($q) use ($selectedIds) {
            $q->whereDoesntHave('combinaciones')
                ->orWhereIn('id', $selectedIds);
        })->get(['id', 'nombre', 'codigo_qr']);
        $grupos = Grupo::orderBy('id', 'asc')->pluck('nombre', 'id');

        return view('combinaciones.edit', compact(
            'data',
            'grupos',
            'disabled',
            'productos',
            'preselected'
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate(array_merge(Combinacion::$rules, [
            'productos' => 'nullable|array',
            'productos.*' => 'integer|distinct|exists:productos,id',
        ]));

        $model = Combinacion::findOrFail($id);
        $ids = array_values(array_unique($request->input('productos', [])));

        // validar usados en OTRA combinación
        if (!empty($ids)) {
            $usados = DB::table('combinaciones_productos')
                ->whereIn('producto_id', $ids)
                ->where('combinacion_id', '!=', $model->id)
                ->exists();

            if ($usados) {
                throw ValidationException::withMessages([
                    'productos' => 'Uno o más productos ya están asignados a otra combinación.',
                ]);
            }
        }

        $model->update([
            'nombre' => $request->nombre,
            'codigo_qr' => $request->codigo_qr,
        ]);

        $model->productos()->sync($ids);

        return redirect()
            ->route("{$this->route}.index")
            ->with('success', 'Actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $data = Combinacion::findOrFail($id);
            $data->delete();

            return redirect()
                ->route("{$this->route}.index")
                ->with('success', 'Eliminado correctamente.');
        } catch (\Throwable $e) {
            return redirect()
                ->route("{$this->route}.index")
                ->with('warning', $e->getMessage());
        }
    }

    public function getSelect(Request $request)
    {
        return response()->json(
            Combinacion::pluck('nombre', 'id')
        );
    }
}
