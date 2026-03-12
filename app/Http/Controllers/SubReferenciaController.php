<?php

namespace App\Http\Controllers;

use App\Models\SubCategoria;
use Illuminate\Http\Request;
use App\Models\SubReferencia;
use App\Traits\DatatableExport;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Console\View\Components\Factory;

class SubReferenciaController extends Controller
{

    use DatatableExport;

    private string $route = 'subreferencias';

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

        $perPage = (new SubReferencia)->getPerPage(); // items por pagina

        $query = SubReferencia::with('subcategoria');

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
        $subreferencias = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('subreferencia.index', compact('subreferencias', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): Factory|View
    {
        $data = new SubReferencia();

        $disabled = false;

        $subcategorias = SubCategoria::pluck('nombre', 'id');

        return view('subreferencia.create', compact(
            'data',
            'subcategorias',
            'disabled'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        request()->validate(SubReferencia::$rules);

        $request['created_by'] = Auth::id();

        $data = SubReferencia::create($request->all());

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Creado correctamente.');
    }

    /**
     * Show the form for see the specified resource.
     */
    public function show(int $id): View
    {
        $data = SubReferencia::findOrFail($id);

        $disabled = true;

        $subcategorias = SubCategoria::pluck('nombre', 'id');

        return view('subreferencia.edit', compact(
            'data',
            'subcategorias',
            'disabled'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): Factory|View
    {
        $data = SubReferencia::find($id);

        $disabled = false;
        $subcategorias = SubCategoria::pluck('nombre', 'id');

        return view('subreferencia.edit', compact(
            'data',
            'subcategorias',
            'disabled'
        ));
    }

    public function update(Request $request, int $id)
    {
        request()->validate(SubReferencia::$rules);

        $model = SubReferencia::findOrFail($id);

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
            $data = SubReferencia::findOrFail($id);
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
        $data = SubReferencia::pluck('nombre', 'id');
        return response()->json($data);
    }

}
