<?php
namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Inventario;
use App\Services\InventarioService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class InventarioController extends Controller
{
    private string $route = 'inventarios';

    public function __construct()
    {
        $this->middleware("can:{$this->route}.index")->only('index');
        $this->middleware("can:{$this->route}.movimientos")->only('movimientos');
        $this->middleware("can:{$this->route}.mover")->only('mover');
        $this->middleware("can:{$this->route}.create")->only('create', 'store');
        $this->middleware("can:{$this->route}.edit")->only('edit', 'update');
        $this->middleware("can:{$this->route}.destroy")->only('destroy');
    }

    // ===== LISTADO DE INVENTARIOS =====
    public function index(Request $request)
    {
        $perPage = (new Inventario)->getPerPage();
        $query = Inventario::with(['producto', 'almacen']);

        if ($request->ajax()) {

            $query = Inventario::select('inventarios.*')
                ->with(['producto', 'almacen']);

            return DataTables::eloquent($query)
                ->addColumn('producto', fn($row) => $row->producto?->nombre ?? '-')
                ->addColumn('codigo_qr', fn($row) => $row->producto?->codigo_qr ?? '-')
                ->addColumn('almacen', fn($row) => $row->almacen?->nombre ?? '-')
                ->addColumn('stock', fn($row) => $row->stock)
                ->addColumn('activo', fn($row) => $row->activo)
                ->addColumn('action', function ($row) {
                    $route = $this->route;
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        $inventarios = $query->orderBy('almacen_id')
            ->orderBy('producto_id')
            ->paginate($perPage);

        return view('inventario.index', compact('inventarios', 'perPage'));
    }

    // ===== CREAR INVENTARIO =====
    public function create(): View
    {
        $productos = Producto::pluck('nombre', 'id');
        $almacenes = Almacen::pluck('nombre', 'id');

        $data = new Inventario(); // Para usar en el form
        $disabled = false;

        return view('inventario.create', compact('productos', 'almacenes', 'data', 'disabled'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'stock' => 'required|integer|min:0',
        ]);

        // Verificar si ya existe inventario para ese producto en ese almacén
        $existe = Inventario::where('producto_id', $request->producto_id)
            ->where('almacen_id', $request->almacen_id)
            ->exists();

        if ($existe) {
            return back()
                ->withErrors(['producto_id' => 'Ya existe inventario para este producto en este almacén.'])
                ->withInput();
        }

        $data = $request->only(['producto_id', 'almacen_id', 'stock']);
        $data['activo'] = $request->has('activo');
        $data['created_by'] = auth()->id();

        Inventario::create($data);

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Inventario creado correctamente.');
    }

    // ===== EDITAR INVENTARIO =====
    public function edit(int $id): View
    {
        $inventario = Inventario::findOrFail($id);
        $productos = Producto::pluck('nombre', 'id');
        $almacenes = Almacen::pluck('nombre', 'id');
        $data = $inventario;
        $disabled = false;

        return view('inventario.edit', compact('data', 'productos', 'almacenes', 'disabled'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'stock' => 'required|integer|min:0',
        ]);

        $inventario = Inventario::findOrFail($id);
        $inventario->update([
            'producto_id' => $request->producto_id,
            'almacen_id' => $request->almacen_id,
            'stock' => $request->stock,
            'activo' => $request->has('activo')
        ]);

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Inventario actualizado correctamente.');
    }

    // ===== ELIMINAR INVENTARIO =====
    public function destroy(int $id): RedirectResponse
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->delete();

        return redirect()->route("{$this->route}.index")
            ->with('success', 'Inventario eliminado correctamente.');
    }

    // ===== MOVIMIENTOS DE STOCK =====
    public function movimientos(int $id): View
    {
        $inventario = Inventario::with(['producto', 'almacen'])->findOrFail($id);
        $movimientos = $inventario->movimientos()->orderBy('id', 'desc')->paginate(30);

        return view('inventario.movimientos', compact('inventario', 'movimientos'));
    }

    // ===== REGISTRAR MOVIMIENTO =====
    public function mover(Request $request): RedirectResponse
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'tipo' => 'required|in:ingreso,salida',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string',
        ]);

        try {
            InventarioService::mover(
                $request->producto_id,
                $request->almacen_id,
                $request->cantidad,
                $request->tipo,
                $request->motivo
            );
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return back()->with('success', 'Movimiento aplicado correctamente.');
    }
}
