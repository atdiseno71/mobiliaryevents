<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Traits\Template;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Class VentaController
 * @package App\Http\Controllers
 */
class VentaController extends Controller
{
    use Template;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ventas = Venta::orderBy('codigo_factura','desc')->paginate();

        return view('venta.index', compact('ventas'))
            ->with('i', (request()->input('page', 1) - 1) * $ventas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accion = 'crear';
        $clientes = Cliente::pluck('nombre','id');
        $usuario = $this->traerUsuario();
        $stocks = [];
        //Relacionado al codigo
        $codigo = 10001;
        $venta = new Venta();
        $ventas = Venta::count();
        if($ventas!=0)$codigo = $codigo + $ventas;
        return view('venta.create', compact('venta','clientes','usuario','codigo','accion','stocks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Venta::$rules);

        $data = [
            'codigo_factura' => $request['codigo_factura'],
            'id_cliente' => $request['id_cliente'],
            'id_comprador' => $request['id_comprador'],
            'productos' => $request['listaProductos'],
            'total' => $request['total'],
        ];
        $respuesta = $this->actualizarProducto($this->decodificar($request['listaProductos']));
        if($respuesta){
            $cliente = Cliente::find($data['id_cliente']);

            $cliente->update([
                'total_compras'=>$cliente->total_compras+1,
                'ultima_compra'=>date('Y-m-d'),
            ]);
            Venta::create($data);
            return redirect()->route('ventas.index')
                ->with('success', 'Venta realizada correctamente.');
        }else{
            return redirect()->route('ventas.index')
                ->with('error', 'Alguno de los productos seleccionados no tiene stock.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $accion = 'editar';
        $venta = Venta::find($id);
        $clientes = Cliente::pluck('nombre','id');
        $usuario = $this->traerUsuario();
        $stocks = $this->traerProductoVendido($venta->productos);
        //Relacionado al codigo
        $codigo = $venta->codigo_factura;
        return view('venta.edit', compact('venta','clientes','usuario','codigo','accion','stocks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Venta $venta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Venta $venta)
    {
        request()->validate(Venta::$rules);

        $data = [
            'codigo_factura' => $request['codigo_factura'],
            'id_cliente' => $request['id_cliente'],
            'id_comprador' => $request['id_comprador'],
            'productos' => $request['listaProductos'],
            'total' => $request['total'],
        ];

        $respuesta = $this->actualizarProducto($this->decodificar($request['listaProductos']));

        if($respuesta){
            $venta->update($data);
            return redirect()->route('ventas.index')
                ->with('success', 'Venta actualizada correctamente. ');
        }else{
            return redirect()->route('ventas.index')
                ->with('error', 'Alguno de los productos seleccionados no tiene stock.');
        }
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $venta = Venta::find($id);
        $cliente = Cliente::find($venta->id_cliente);
        $this->devolverProductos($this->decodificar($venta->productos));
        $cliente->update([
            'total_compras'=>$cliente->total_compras-1,
            'ultima_compra'=>date('Y-m-d'),
        ]);

        $venta->delete();

        return redirect()->route('ventas.index')
            ->with('success', 'Venta eliminada correctamente. ');
    }

    /**
     * It generates a PDF with the data of the sales made in a certain period of time.
     * </code>
     *
     * @param Request request The request object.
     *
     * @return The PDF is being returned.
     */
    public function rangoPDF(Request $request){
        $total = 0;
        $promedio = 0;
        $cantidad = 0;
        $i = 1;
        if($request['fechaInicial'] == null)$ventas = Venta::orderBy('id', 'asc')->get();
        else if($request['fechaInicial'] == $request['fechaFinal'])$ventas = Venta::where('fecha_venta','LIKE','%'.$request['fechaFinal'].'%')->get();
        else$ventas = Venta::whereBetween('fecha_venta',[$request['fechaInicial'],$request['fechaFinal']])->get();
        //Generamos el pdf
        $pdf = PDF::loadview('venta.show', compact('ventas','total','promedio','cantidad','i'));
        return $pdf->stream('reporte.pdf');
    }

    /**
     * It takes a JSON string as an argument, decodes it, loops through the decoded array, and returns
     * an array of the stock values of the products in the decoded array
     *
     * @param lista the list of products that are being sold
     */
    public function traerProductoVendido($lista){
        $listaProducto = json_decode($lista, true);
        $stocks = [];
        foreach ($listaProducto as $listaproducto) {
            $producto = Producto::where('opcion','1')->where('id',$listaproducto['id'])->get();
            array_push($stocks, $producto[0]->stock + $listaproducto['cantidad']);
        }
        return $stocks;
    }

    /**
     * It returns a JSON object of all the products in the database
     */
    public function traerProductos(Request $request)
    {
        $productos = Producto::where('opcion','1')->get();
        $respuesta = $this->devolverListaProductoVenta($productos);
        return response(json_encode($respuesta),200)->header('Content-type','text/plain');
    }

    /**
     * It returns a JSON object of the product with the name
     *
     * @param nombre The name of the product
     */
    public function traerProducto(Request $request)
    {
        $producto = Producto::where('id',$request['id'])->where('opcion','1')->get();
        $respuesta = $this->devolverListaProductoVenta($producto);
        return response(json_encode($respuesta),200)->header('Content-type','text/plain');
    }

    /**
     * The function facturaVenta() is called from a route, and it returns a PDF file
     *
     * @return The PDF is being returned.
     */
    public function facturaVenta($id){
        $venta = Venta::find($id);
        $codigo = $venta->codigo_factura;
        $pdf = PDF::loadview('venta.factura',compact('venta','codigo'));
        $pdf->setPaper('b7', 'portrait');
        return $pdf->stream('reporte.pdf');
    }

}
