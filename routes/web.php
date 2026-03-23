<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\TipoGastoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\Auth\LoginController;

// ****************************************************
// modulos funcionales para el proyecto de inventario
// ****************************************************
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\RemisionController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\CombinacionController;
use App\Http\Controllers\SubCategoriaController;
use App\Http\Controllers\SubReferenciaController;
use App\Http\Controllers\MovimientoInventarioController;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::group(['middleware' => 'auth'], function () {
    //cargan los modulos basicos de un pos
    Route::get('home', [HomeController::class, 'index'])->name('home.index');

    Route::resource('gastos', GastoController::class)
        ->only('index', 'create', 'store', 'edit', 'update', 'destroy')->names('gastos');
    Route::resource('tipo-gastos', TipoGastoController::class)
        ->only('index', 'create', 'store', 'edit', 'update', 'destroy')->names('tipo-gastos');
    Route::resource('ventas', VentaController::class)
        ->only('index', 'create', 'store', 'edit', 'update', 'destroy')->names('ventas');
    Route::resource('compras', CompraController::class)
        ->only('index', 'create', 'store', 'edit', 'update', 'destroy')->names('compras');
    //Modulos para generar reportes
    Route::get('productos/pdf', [ProductoController::class, 'productosPDF'])->name('productos.reporte.pdf');
    Route::get('gastos/pdf', [GastoController::class, 'PDF'])->name('gastos.reporte.pdf');
    Route::get('ventas/pdf', [VentaController::class, 'rangoPDF'])->name('ventas.rango.pdf');
    Route::post('ventas/pdf', [VentaController::class, 'rangoPDF']);
    //Modulo con formulario para generar reporte de compra
    Route::get('compras/pdf', [CompraController::class, 'traerFormulario'])->name('compras.rango.pdf');
    Route::post('compras/pdf', [CompraController::class, 'PDF'])->name('compras.pdf');
    //Traer variables para llenar los modulos
    //Listar todos los productos en el select
    Route::post('ventas/traer/productos', [VentaController::class, 'traerProductos']);
    Route::post('ventas/{id}/traer/productos', [VentaController::class, 'traerProductos']);
    Route::post('compras/traer/productos', [CompraController::class, 'traerProductos']);
    Route::post('compras/{id}/traer/productos', [CompraController::class, 'traerProductos']);
    //Traer informacion del producto seleccionado
    Route::post('ventas/traer/seleccionado', [VentaController::class, 'traerProducto']);
    Route::post('ventas/{id}/traer/seleccionado', [VentaController::class, 'traerProducto']);
    Route::post('compras/traer/seleccionado', [CompraController::class, 'traerSeleccionado']);
    Route::post('compras/{id}/traer/seleccionado', [CompraController::class, 'traerSeleccionado']);
    //Traer las facturas generadas
    Route::get('ventas/factura/{id}', [VentaController::class, 'facturaVenta'])->name('ventas.factura');
    Route::get('compras/factura/{id}', [CompraController::class, 'facturaCompra'])->name('compras.factura');

    // ****************************************************
    // modulos funcionales para el proyecto de inventario
    // ****************************************************
    Route::resource('clientes', ClienteController::class)->names('clientes');
    Route::resource('proveedores', ProveedorController::class)->names('proveedores');
    Route::resource('usuarios', UserController::class)->names('usuarios');
    Route::resource('subreferencias', SubReferenciaController::class)->names('subreferencias');
    Route::resource('almacenes', AlmacenController::class)->names('almacenes');
    Route::resource('grupos', GrupoController::class)->names('grupos');
    Route::resource('categorias', CategoriaController::class)->names('categorias');
    Route::resource('subcategorias', SubCategoriaController::class)->names('subcategorias');
    Route::resource('productos', ProductoController::class)->names('productos');
    Route::resource('combinaciones', CombinacionController::class)->names('combinaciones');
    Route::resource('remisiones', RemisionController::class)->names('remisiones');
    Route::resource('cotizaciones', CotizacionController::class)->names('cotizaciones');
    Route::resource('tipos-evento', TipoEventoController::class);
    Route::resource('movimientos', MovimientoInventarioController::class);

    Route::prefix('inventarios')->group(function () {
        // CRUD principal
        Route::get('/', [InventarioController::class, 'index'])->name('inventarios.index');
        Route::get('/create', [InventarioController::class, 'create'])->name('inventarios.create');
        Route::post('/', [InventarioController::class, 'store'])->name('inventarios.store');
        Route::get('/{id}/show', [InventarioController::class, 'edit'])->name('inventarios.show');
        Route::get('/{id}/edit', [InventarioController::class, 'edit'])->name('inventarios.edit');
        Route::put('/{id}', [InventarioController::class, 'update'])->name('inventarios.update');
        Route::delete('/{id}', [InventarioController::class, 'destroy'])->name('inventarios.destroy');

        // Movimientos y stock
        Route::get('/{id}/movimientos', [InventarioController::class, 'movimientos'])->name('inventarios.movimientos');
        Route::post('/mover', [InventarioController::class, 'mover'])->name('inventarios.mover');
    });

    Route::resource('roles', RoleController::class);

    // ruta unica de la empresa
    Route::get('empresa', [EmpresaController::class, 'index'])->name('empresa.index');
    Route::put('empresa/{empresa}', [EmpresaController::class, 'update'])->name('empresa.update');

    //Cambiar contrasena de usuarios
    Route::get('cambiar-contrasena', [UserController::class, 'mostrarContrasena'])->name('usuario.form.cambiar-contrasena');
    Route::patch('cambiar-contrasena', [UserController::class, 'cambiarContrasena'])->name('usuario.cambiar-contrasena');

    // para selects
    Route::get('/departamentos/{pais}', [ClienteController::class, 'getDepartamentosByPais'])->name('departamentos.by-pais');
    Route::get('/ciudades/{departamento}', [ClienteController::class, 'getCiudadesByDepartamento'])->name('ciudades.by-departamento');
    Route::get('get_grupos', [GrupoController::class, 'getSelect'])->name('grupos.get_grupos');
    Route::get('/grupos/{id}/categorias', [ProductoController::class, 'getCategorias']);
    Route::get('/categorias/{id}/subcategorias', [ProductoController::class, 'getSubcategorias']);
    Route::get('/subcategorias/{id}/subreferencias', [ProductoController::class, 'getSubreferencias']);

    Route::get('api/remisiones/{id}', [RemisionController::class, 'getRemision'])->name('api.remisiones.show');

    // pdfs del sistema inventario
    Route::get('remisiones/{id}/pdf', [RemisionController::class, 'pdf'])
        ->name('remisiones.pdf');

});

Route::
        namespace('Auth')->group(function () {
            Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
            Route::post('login', [LoginController::class, 'login']);
            Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        });
