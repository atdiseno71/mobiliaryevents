<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Yajra\DataTables\Facades\DataTables;

class CategoriaController extends Controller
{
    private string $route = 'categorias';

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
        $perPage = (new Categoria)->getPerPage();
        $query = Categoria::with('grupo');

        if ($request->ajax()) {
            return DataTables::eloquent($query)
                ->addColumn('grupo', fn($row) => $row->grupo?->nombre ?? '-')
                ->addColumn('action', function ($row) {
                    $route = 'categorias';
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        $categorias = $query->paginate($perPage);
        return view('categoria.index', compact('categorias', 'perPage'));
    }

    public function create(): Factory|View
    {
        $data = new Categoria();
        $disabled = false;
        $grupos = Grupo::pluck('nombre', 'id');

        return view('categoria.create', compact('data', 'disabled', 'grupos'));
    }

    public function store(Request $request): RedirectResponse
    {
        request()->validate(Categoria::$rules);

        $request['created_by'] = Auth::id();

        Categoria::create($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(int $id): View
    {
        $data = Categoria::with('grupo')->findOrFail($id);
        $disabled = true;
        $grupos = Grupo::pluck('nombre', 'id');

        return view('categoria.edit', compact('data', 'disabled', 'grupos'));
    }

    public function edit(int $id): Factory|View
    {
        $data = Categoria::findOrFail($id);
        $disabled = false;
        $grupos = Grupo::pluck('nombre', 'id');

        return view('categoria.edit', compact('data', 'disabled', 'grupos'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        request()->validate(Categoria::$rules);

        $model = Categoria::findOrFail($id);

        $model->update($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = Categoria::findOrFail($id);

            if ($data->subcategorias()->exists()) {
                return redirect()->route("{$this->route}.index")
                    ->with('warning', 'No se puede eliminar esta categoría porque tiene subcategorías asociadas.');
            }

            $data->delete();

            return redirect()->route("{$this->route}.index")
                ->with('success', 'Categoría eliminada correctamente.');
        } catch (\Throwable $e) {
            return redirect()->route("{$this->route}.index")
                ->with('warning', $e->getMessage());
        }
    }

    public function getSelect(Request $request)
    {
        $data = Categoria::pluck('nombre', 'id');
        return response()->json($data);
    }
}
