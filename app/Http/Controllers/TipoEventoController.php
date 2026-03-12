<?php

namespace App\Http\Controllers;

use App\Models\TipoEvento;
use Illuminate\Http\Request;
use App\Traits\DatatableExport;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Console\View\Components\Factory;

class TipoEventoController extends Controller
{

    use DatatableExport;

    private string $route = 'tipos-evento';

    public function __construct()
    {
        $this->middleware("can:{$this->route}.index")->only('index');
        $this->middleware("can:{$this->route}.create")->only('create', 'store');
        $this->middleware("can:{$this->route}.show")->only('show');
        $this->middleware("can:{$this->route}.edit")->only('edit', 'update');
        $this->middleware("can:{$this->route}.destroy")->only('destroy');
        // Para selects
        $this->middleware("can:getSelects")->only('getSelect');
    }

    public function index(Request $request)
    {
        $perPage = (new TipoEvento)->getPerPage();

        $query = TipoEvento::query();

        if ($request->ajax()) {
            $this->dtApplyLength($request, $perPage);

            return DataTables::eloquent($query)
                ->addColumn('action', function ($row) {
                    $route = $this->route;
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        $tiposEvento = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('tipo-evento.index', compact('tiposEvento', 'perPage'));
    }

    public function create(): Factory|View
    {
        $data = new TipoEvento();
        $disabled = false;

        return view('tipo-evento.create', compact('data', 'disabled'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(TipoEvento::$rules);

        $request['created_by'] = Auth::id();

        TipoEvento::create($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Creado correctamente.');
    }

    public function show(int $id): View
    {
        $data = TipoEvento::findOrFail($id);
        $disabled = true;

        return view('tipo-evento.edit', compact('data', 'disabled'));
    }

    public function edit(int $id): Factory|View
    {
        $data = TipoEvento::findOrFail($id);
        $disabled = false;

        return view('tipo-evento.edit', compact('data', 'disabled'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate(TipoEvento::$rules);

        $model = TipoEvento::findOrFail($id);
        $model->update($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Actualizado correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = TipoEvento::findOrFail($id);
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
        $data = TipoEvento::pluck('nombre', 'id');
        return response()->json($data);
    }
}
