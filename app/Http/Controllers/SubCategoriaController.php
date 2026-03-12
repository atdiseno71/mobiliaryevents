<?php

namespace App\Http\Controllers;

use App\Models\SubCategoria;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Yajra\DataTables\Facades\DataTables;

class SubCategoriaController extends Controller
{
    private string $route = 'subcategorias';

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
        $perPage = (new SubCategoria)->getPerPage();
        $query = SubCategoria::with('categoria');

        if ($request->ajax()) {
            return DataTables::eloquent($query)
                ->addColumn('categoria', fn($row) => $row->categoria?->nombre ?? '-')
                ->addColumn('action', function ($row) {
                    $route = 'subcategorias';
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        $subcategorias = $query->paginate($perPage);
        return view('subcategoria.index', compact('subcategorias', 'perPage'));
    }

    public function create(): Factory|View
    {
        $data = new SubCategoria();
        $disabled = false;
        $categorias = Categoria::pluck('nombre', 'id');

        return view('subcategoria.create', compact('data', 'disabled', 'categorias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(SubCategoria::$rules);

        $request['created_by'] = Auth::id();

        SubCategoria::create($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Subcategoría creada correctamente.');
    }

    public function show(int $id): View
    {
        $data = SubCategoria::with('categoria')->findOrFail($id);
        $disabled = true;
        $categorias = Categoria::pluck('nombre', 'id');

        return view('subcategoria.edit', compact('data', 'disabled', 'categorias'));
    }

    public function edit(int $id): Factory|View
    {
        $data = SubCategoria::findOrFail($id);
        $disabled = false;
        $categorias = Categoria::pluck('nombre', 'id');

        return view('subcategoria.edit', compact('data', 'disabled', 'categorias'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate(SubCategoria::$rules);

        $model = SubCategoria::findOrFail($id);

        $model->update($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Subcategoría actualizada correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = SubCategoria::findOrFail($id);
            $data->delete();

            return redirect()->route("{$this->route}.index")
                ->with('success', 'Subcategoría eliminada correctamente.');
        } catch (\Throwable $e) {
            return redirect()->route("{$this->route}.index")
                ->with('warning', $e->getMessage());
        }
    }

    public function getSelect(Request $request)
    {
        $data = SubCategoria::pluck('nombre', 'id');
        return response()->json($data);
    }
}
