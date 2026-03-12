<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Grupo;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\View\View;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Traits\ImageTrait;
use App\Models\SubCategoria;
use Illuminate\Http\Request;
use App\Models\SubReferencia;
use App\Services\InventarioService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class ProductoController extends Controller
{

    use ImageTrait;

    private string $route = 'productos';
    private string $folder = 'productos';

    public function __construct()
    {
        $this->middleware("can:{$this->route}.index")->only('index');
        $this->middleware("can:{$this->route}.create")->only('create', 'store');
        $this->middleware("can:{$this->route}.show")->only('show');
        $this->middleware("can:{$this->route}.edit")->only('edit', 'update');
        $this->middleware("can:{$this->route}.destroy")->only('destroy');
    }

    public function index(Request $request)
    {
        $perPage = (new Producto)->getPerPage();

        $query = Producto::with(['grupo', 'categoria', 'subcategoria', 'marca'])
            ->select('productos.*');

        if ($request->ajax()) {
            return DataTables::eloquent($query)
                ->addColumn('action', function ($row) {
                    $route = $this->route;
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->editColumn('marca', function ($row) {
                    return $row->marca ? $row->marca->nombre : '-';
                })
                ->editColumn('grupo', function ($row) {
                    return $row->grupo ? $row->grupo->nombre : '-';
                })
                ->editColumn('categoria', function ($row) {
                    return $row->categoria ? $row->categoria->nombre : '-';
                })
                ->editColumn('subcategoria', function ($row) {
                    return $row->subcategoria ? $row->subcategoria->nombre : '-';
                })
                ->editColumn('activo', fn($row) => $row->activo ? '✅' : '❌')
                ->rawColumns(['action'])
                ->toJson();
        }

        $productos = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('producto.index', compact('productos', 'perPage'));
    }

    public function create(): View
    {
        $producto = new Producto();
        $disabled = false;

        $grupos = Grupo::pluck('nombre', 'id');
        $categorias = Categoria::pluck('nombre', 'id');
        $subcategorias = SubCategoria::pluck('nombre', 'id');
        $subreferencias = SubReferencia::pluck('nombre', 'id');
        $marcas = Marca::pluck('nombre', 'id');

        return view('producto.create', compact(
            'producto',
            'grupos',
            'categorias',
            'subcategorias',
            'subreferencias',
            'marcas',
            'disabled'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(Producto::$rules);

        // Preparamos datos (sin imagen)
        $data = $request->except('imagen');
        $data['created_by'] = Auth::id();

        // 1) Crear el producto primero para obtener ID
        $producto = Producto::create($data);

        // ===============================
        // CREAR INVENTARIO AUTOMÁTICO
        // ===============================
        $almacenDefault = 1; // almacén por defecto

        // crear movimiento de entrada
        InventarioService::mover(
            $producto->id,
            $almacenDefault,
            1,
            'ingreso',
            'Inventario inicial - por creación del producto'
        );

        // Inventario::create([
        //     'producto_id' => $producto->id,
        //     'almacen_id' => $almacenDefault,
        //     'stock' => 0,
        //     'activo' => true,
        //     'created_by' => Auth::id(),
        // ]);

        // 2) Si subió imagen, procesarla
        if ($request->hasFile('imagen')) {
            try {
                // Carpeta por producto: productos/_ID
                $folder = "{$this->folder}";

                // Subir y convertir a webp (el trait crea la carpeta si hace falta)
                $filename = $this->uploadWebp($request->file('imagen'), $folder);

                if ($filename) {
                    // Actualizar nombre de archivo en DB
                    $producto->update(['imagen' => $filename]);
                } else {
                    // Si no se generó nombre, eliminar producto creado por seguridad
                    $producto->delete();
                    return redirect()->back()->withInput()
                        ->with('warning', 'No se pudo procesar la imagen. Intenta de nuevo.');
                }
            } catch (\Throwable $e) {
                // Si hay error, limpiamos y devolvemos mensaje
                if ($producto && $producto->exists) {
                    $producto->delete();
                }
                return redirect()->back()->withInput()
                    ->with('warning', 'Ocurrió un error al subir la imagen: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(int $id): View
    {
        $producto = Producto::with(['grupo', 'categoria', 'subcategoria', 'marca'])->findOrFail($id);
        $disabled = true;

        $grupos = Grupo::pluck('nombre', 'id');
        $categorias = Categoria::pluck('nombre', 'id');
        $subcategorias = SubCategoria::pluck('nombre', 'id');
        $subreferencias = SubReferencia::pluck('nombre', 'id');
        $marcas = Marca::pluck('nombre', 'id');

        return view('producto.edit', compact(
            'producto',
            'grupos',
            'categorias',
            'subcategorias',
            'subreferencias',
            'marcas',
            'disabled'
        ));
    }

    public function edit(int $id): View
    {
        $producto = Producto::findOrFail($id);
        $disabled = false;

        $grupos = Grupo::pluck('nombre', 'id');
        $categorias = Categoria::pluck('nombre', 'id');
        $subcategorias = SubCategoria::pluck('nombre', 'id');
        $subreferencias = SubReferencia::pluck('nombre', 'id');
        $marcas = Marca::pluck('nombre', 'id');

        return view('producto.edit', compact(
            'producto',
            'grupos',
            'categorias',
            'subcategorias',
            'subreferencias',
            'marcas',
            'disabled'
        ));
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate(Producto::rulesForUpdate($producto->id));

        $data = $request->except('imagen');

        $folder = "{$this->folder}";

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                $oldPath = storage_path("app/public/{$folder}/{$producto->imagen}");
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $data['imagen'] = ImageTrait::uploadWebp($request->file('imagen'), $folder);
        }

        $producto->update($data);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function getCategorias($grupo_id)
    {
        $data = \App\Models\Categoria::where('grupo_id', $grupo_id)
            ->orderBy('nombre')
            ->pluck('nombre', 'id');

        return response()->json($data);
    }

    public function getSubcategorias($categoria_id)
    {
        $data = \App\Models\SubCategoria::where('categoria_id', $categoria_id)
            ->orderBy('nombre')
            ->pluck('nombre', 'id');

        return response()->json($data);
    }

    public function getSubreferencias($subcategoria_id)
    {
        $data = \App\Models\SubReferencia::where('subcategoria_id', $subcategoria_id)
            ->orderBy('nombre')
            ->pluck('nombre', 'id');

        return response()->json($data);
    }

}
