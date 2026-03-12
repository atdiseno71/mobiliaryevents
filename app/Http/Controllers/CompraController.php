<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Cliente;
use App\Models\Producto;
use App\Traits\Template;
use Illuminate\Http\Request;
use App\Exports\ComprasExport;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class CompraController
 * @package App\Http\Controllers
 */
class CompraController extends Controller
{

    use Template;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $compras = Compra::orderBy('codigo_factura', 'desc')->paginate();

        return view('compra.index', compact('compras'))
            ->with('i', (request()->input('page', 1) - 1) * $compras->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accion = 'crear';
        $clientes = Cliente::pluck('nombre', 'id');
        $usuario = $this->traerUsuario();
        //Relacionado al codigo
        $codigo = 100001;
        $compra = new Compra();
        $compras = Compra::count();
        if ($compras != 0)
            $codigo = $codigo + $compras;
        return view('compra.create', compact('compra', 'clientes', 'usuario', 'codigo', 'accion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Compra::$rules);

        $data = [
            'codigo_factura' => $request['codigo_factura'],
            'id_comprador' => $request['id_comprador'],
            'id_cliente' => $request['id_cliente'],
            'tipo_cantidad' => $request['tipo_cantidad'],
            'productos' => $request['listaProductos'],
            'total' => $request['total'],
        ];

        $cliente = Cliente::find($data['id_cliente']);

        $cliente->update([
            'total_compras' => $cliente->total_compras + 1,
            'ultima_compra' => date('Y-m-d'),
        ]);

        Compra::create($data);

        return redirect()->route('compras.index')
            ->with('success', 'Compra realizada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $compra = Compra::find($id);
        $accion = 'editar';
        $clientes = Cliente::pluck('nombre', 'id');
        $usuario = $this->traerUsuario();
        //Relacionado al codigo
        $codigo = $compra->codigo_factura;
        // return 'funca';
        return view('compra.edit', compact('compra', 'clientes', 'usuario', 'codigo', 'accion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Compra $compra
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Compra $compra)
    {
        request()->validate(Compra::$rules);

        $data = [
            'codigo_factura' => $request['codigo_factura'],
            'id_comprador' => $request['id_comprador'],
            'id_cliente' => $request['id_cliente'],
            'tipo_cantidad' => $request['tipo_cantidad'],
            'productos' => $request['listaProductos'],
            'total' => $request['total'],
        ];

        $compra->update($data);

        return redirect()->route('compras.index')
            ->with('success', 'Compra actualizada correctamente.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $compra = Compra::find($id);
        $cliente = Cliente::find($compra->id_cliente);

        $cliente->update([
            'total_compras' => $cliente->total_compras - 1,
            'ultima_compra' => date('Y-m-d'),
        ]);

        $compra->delete();

        return redirect()->route('compras.index')
            ->with('success', 'Compra eliminada correctamente.');
    }

    public function PDF(Request $request)
    {
        $total = 0;
        $promedio = 0;
        $cantidad = 0;
        $i = 1;

        $fechaInicial = $request->fechaInicial ?? now('America/Bogota')->toDateString();
        $fechaFinal   = $request->fechaFinal   ?? now('America/Bogota')->toDateString();
        $tipoReporte = $request->tipo_reporte ?? 'excel';

        $productos = Producto::where('opcion', '0')->get();
        $listaProductos = $this->devolverListaCompra($productos, $fechaInicial, $fechaFinal);

        // dd($listaProductos);

        if ($tipoReporte === 'excel') {
            return Excel::download(
                new ComprasExport($listaProductos),
                'reporte_compras.xlsx'
            );
        }

        $pdf = PDF::loadView('compra.show', compact('productos', 'total', 'promedio', 'cantidad', 'i', 'listaProductos'));
        return $pdf->stream('reporte.pdf');
    }

    /**
     * I'm trying to generate a PDF file from a view, but I'm getting an error
     *
     * @param id The id of the record you want to print
     */
    public function facturaCompra($id)
    {
        $compra = Compra::find($id);
        $codigo = $compra->codigo_factura;
        $pdf = PDF::loadview('compra.factura', compact('compra', 'codigo'));
        // $pdf->setPaper('b7', 'portrait');

        // Media carta vertical: 8.5 x 5.5 pulgadas (portrait)
        // 1 pulgada = 72pt -> 8.5*72 = 612, 5.5*72 = 396
        // $pdf->setPaper([0, 0, 612, 396], 'portrait'); // Media carta vertical
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('factura.pdf');
    }

    /**
     * It returns a JSON response with a list of products
     *
     * @param Request request The request object.
     *
     * @return A list of products
     */
    public function traerProductos(Request $request)
    {
        $productos = Producto::where('opcion', '0')->get();
        $respuesta = $this->devolverListaProductoCompra($productos);
        return response(json_encode($respuesta), 200)->header('Content-type', 'text/plain');
    }

    /**
     * It takes a request, finds a product with the id of the request and the opcion of 0, then returns
     * a json response of the product
     *
     * @param Request request The request object.
     *
     * @return a json object with the product information.
     */
    public function traerSeleccionado(Request $request)
    {
        $producto = Producto::where('id', $request['id'])->where('opcion', '0')->get();
        $respuesta = $this->devolverListaProductoCompra($producto);
        return response(json_encode($respuesta), 200)->header('Content-type', 'text/plain');
    }

    /**
     * It returns a view called form-pdf
     *
     * @return The view 'compra.form-pdf'
     */
    public function traerFormulario()
    {
        date_default_timezone_set('America/Bogota');
        $fecha = date('Y-m-d');
        return view('compra.form-pdf', compact('fecha'));
    }

}
