<?php

namespace App\Http\Controllers;

use App\Models\TipoGasto;
use App\Models\Gasto;
use Illuminate\Http\Request;

/**
 * Class TipoGastoController
 * @package App\Http\Controllers
 */
class TipoGastoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tipoGastos = TipoGasto::paginate();

        return view('tipo-gasto.index', compact('tipoGastos'))
            ->with('i', (request()->input('page', 1) - 1) * $tipoGastos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tipoGasto = new TipoGasto();
        return view('tipo-gasto.create', compact('tipoGasto'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(TipoGasto::$rules);

        $tipoGasto = TipoGasto::create($request->all());

        return redirect()->route('tipo-gastos.index')
            ->with('success', 'El tipo de gasto se creo correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipoGasto = TipoGasto::find($id);

        return view('tipo-gasto.edit', compact('tipoGasto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  TipoGasto $tipoGasto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TipoGasto $tipoGasto)
    {
        request()->validate(TipoGasto::$rules);

        $tipoGasto->update($request->all());

        return redirect()->route('tipo-gastos.index')
            ->with('success', 'El tipo de gasto se actualizo correctamente.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $mensaje = 'El tipo de gasto se elimino correctamente.';
        $tipo = 'success';
        $categoria = TipoGasto::find($id);
        $cateConProductos = Gasto::where('id_gasto','=',$categoria->id)->get();
        if(count($cateConProductos) == 0)$categoria->delete();
        else{
            $mensaje = 'No se puede eliminar, porque ya contiene gastos.';
            $tipo = 'error';
        }
        return redirect()->route('tipo-gastos.index')
            ->with($tipo, $mensaje);
    }
}
