<?php
namespace App\Traits;

use App\Models\User;
use App\Models\Compra;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;

trait Template
{

    /**
     * It returns an array with the user's id and name
     *
     * @return The user's id and name.
     */
    public function traerUsuario(){
        $usuario = [
            'id'=>auth()->id(),
            'nombre'=>auth()->user()->name,
        ];
        return $usuario;
    }

   /**
    * It takes a JSON string and returns an array
    *
    * @param lista The list of items you want to decode.
    *
    * @return the decoded list.
    */
    public function decodificar($lista){
        $listaDecodificada = json_decode($lista, true);
        return $listaDecodificada;
    }

    /**
     * It takes an array of objects, and updates the database with the values of the objects
     *
     * @param lista array of objects
     */
    public function actualizarProducto($lista){
        foreach ($lista as $lis) {
            $producto = Producto::find($lis['id']);
            if($producto->stock != 0){
                $data = [
                    'stock' => $lis['stock']
                ];
                $producto->update($data);
            }else {
                return false;
            }
        }
        return true;
    }

    public function devolverProductos($lista){
        foreach ($lista as $lis) {
            $producto = Producto::find($lis['id']);
            $data = [
                'stock' => $producto->stock + $lis['cantidad']
            ];
            $producto->update($data);
        }
    }

    public function devolverListaProductoCompra($productos){
        $respuesta = [];
        foreach ($productos as $listaproducto) {
            array_push($respuesta, [
                'id' => $listaproducto['id'],
                'nombre' => $listaproducto->nombre,
                'combi_nombre' => $listaproducto->nombre . ' ' .$listaproducto->categoria->nombre,
                'peso' => $listaproducto->categoria->kilos,
                'opcion' => $listaproducto->opcion
            ]);
        }
        return $respuesta;
    }
    /**
     * Genera una lista detallada de compras asociadas a cada producto, filtrando por fechas si se especifica.
     *
     * @param array $productos Lista de productos a procesar. Cada elemento debe ser un objeto o array con al menos el campo 'id'.
     * @param string|null $fechaInicial Fecha inicial del filtro en formato 'Y-m-d'. Si es null, no se filtra por fecha.
     * @param string|null $fechaFinal Fecha final del filtro en formato 'Y-m-d'. Si es igual a la inicial, solo se filtra por ese día.
     *
     * @return array Retorna un arreglo donde cada elemento representa un producto con sus datos y las compras asociadas.
     *
     * Detalle del funcionamiento:
     * - Por cada producto recibido, busca todas las compras en las que aparece ese producto.
     * - Si no se especifican fechas, trae todas las compras del producto.
     * - Si la fecha inicial y final son iguales, filtra solo por ese día.
     * - Si ambas fechas son distintas, filtra por el rango entre ambas.
     * - Para cada compra encontrada, agrega el nombre del cliente y del comprador.
     * - Devuelve un arreglo con la información del producto y un subarreglo de las compras encontradas.
     */
    public function devolverListaCompra($productos, $fechaInicial,$fechaFinal){
        $respuesta = [];
        foreach ($productos as $listaproducto) {
            $respuestaProductos = [
                'id' => $listaproducto['id'],
                'nombre' => $listaproducto->nombre,
                'combi_nombre' => $listaproducto->nombre . ' ' .$listaproducto->categoria->nombre,
                'peso' => $listaproducto->categoria->kilos,
                'opcion' => $listaproducto->opcion,
                'total' => 0,
                'cantidades' => 0,
                'promedio' => 0,
            ];
            if($fechaInicial == null)$compraProductos = Compra::where('productos','like','%'.'"id":"'.$respuestaProductos['id'].'"%')->orderBy('id', 'asc')->get();
            else if($fechaInicial == $fechaFinal)$compraProductos = Compra::where('productos','like','%'.'"id":"'.$respuestaProductos['id'].'"%')->where('fecha_venta','LIKE','%'.$fechaFinal.'%')->get();
            else$compraProductos = Compra::where('productos','like','%'.'"id":"'.$respuestaProductos['id'].'"%')->whereBetween('fecha_venta',[$fechaInicial,$fechaFinal])->get();
            // $compraProductos = Compra::where('productos','like','%'.'"id":"'.$respuestaProductos['id'].'"%')->where('fecha_venta','like','%'.$fecha.'%')->get();
            $productosAdjuntar = [];
            foreach($compraProductos as $asd){
                $asd['cliente'] = Cliente::find($asd->id_cliente)->nombre;
                $asd['comprador'] = User::find($asd->id_comprador)->name;
                array_push($productosAdjuntar, $asd);
            }
            $respuestaProductos = array_merge($respuestaProductos, [
                'productos' => $productosAdjuntar
            ]);
            array_push($respuesta, $respuestaProductos);
        }
        return $respuesta;
    }

    public function devolverListaProductoVenta($productos){
        $respuesta = [];
        foreach ($productos as $listaproducto) {
            array_push($respuesta, [
                'id' => $listaproducto['id'],
                'nombre' => $listaproducto->nombre,
                'combi_nombre' => $listaproducto->nombre . ' ' .$listaproducto->categoria->nombre,
                'stock' => $listaproducto->stock,
                'opcion' => $listaproducto->opcion
            ]);
        }
        return $respuesta;
    }

}
