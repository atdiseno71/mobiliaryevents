<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Traits\DatatableExport;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Console\View\Components\Factory;

class GrupoController extends Controller
{

    use DatatableExport;

    private string $route = 'grupos';

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

    public function index(Request $request)
    {

        $perPage = (new Grupo)->getPerPage(); // items por pagina

        $query = Grupo::query();

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
        $grupos = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('grupo.index', compact('grupos', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): Factory|View
    {
        $data = new Grupo();

        $disabled = false;

        return view('grupo.create', compact('data', 'disabled'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        request()->validate(Grupo::$rules);

        $request['created_by'] = Auth::id();

        $data = Grupo::create($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Creado correctamente.');
    }

    /**
     * Show the form for see the specified resource.
     */
    public function show(int $id): View
    {
        $data = Grupo::findOrFail($id);

        $disabled = true;

        return view('grupo.edit', compact('data', 'disabled'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): Factory|View
    {
        $data = Grupo::find($id);

        $disabled = false;

        return view('grupo.edit', compact('data', 'disabled'));
    }

    public function update(Request $request, int $id)
    {
        request()->validate(Grupo::$rules);

        $model = Grupo::findOrFail($id);

        $model->update($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Actualizado correctamente');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $data = Grupo::findOrFail($id);
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
        $data = Grupo::pluck('name', 'id');
        return response()->json($data);
    }

}
