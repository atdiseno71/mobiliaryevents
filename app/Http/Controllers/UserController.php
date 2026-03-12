<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Compra;
use App\Models\Venta;
use Illuminate\View\View;
use App\Models\Departamento;
use Illuminate\Http\Request;
use App\Traits\DatatableExport;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{

    use DatatableExport;

    private string $route = 'usuarios';


    /**
     * Mostrar listado de usuarios
     */

    public function index(Request $request)
    {
        $perPage = (new User)->getPerPage(); // items por pagina

        $query = User::query();

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
        $users = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('user.index', compact('users', 'perPage'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): View
    {
        $user = new User();

        $disabled = false;

        $paises = Pais::pluck('nombre', 'id');
        $departamentos = Departamento::pluck('nombre', 'id');
        $ciudades = Ciudad::pluck('nombre', 'id');

        $roles = Role::pluck('name', 'id');

        return view('user.create', compact('user', 'paises', 'departamentos', 'ciudades', 'roles', 'disabled'));
    }

    /**
     * Guardar un nuevo usuario
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = array_merge(User::$rules, [
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'required|min:6|confirmed'
        ]);

        $validated = $request->validate($rules);

        // Subir imagen si existe
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('users', 'public');
        }

        // Encriptar contraseña
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $role = Role::findById($validated['nivel'], 'web');
        $user->syncRoles([$role]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Show the form for see the specified resource.
     */
    public function show(int $id): View
    {
        $user = User::findOrFail($id);

        $disabled = true;

        $paises = Pais::pluck('nombre', 'id');
        $departamentos = Departamento::pluck('nombre', 'id');
        $ciudades = Ciudad::pluck('nombre', 'id');
        $roles = Role::pluck('name', 'id');

        return view('user.edit', compact('user', 'paises', 'departamentos', 'ciudades', 'roles', 'disabled'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(int $id): View
    {
        $user = User::findOrFail($id);

        $disabled = false;

        $paises = Pais::pluck('nombre', 'id');
        $departamentos = Departamento::pluck('nombre', 'id');
        $ciudades = Ciudad::pluck('nombre', 'id');
        $roles = Role::pluck('name', 'id');

        return view('user.edit', compact('user', 'paises', 'departamentos', 'ciudades', 'roles', 'disabled'));
    }

    /**
     * Actualizar usuario existente
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $rules = [
            'codigo' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tipo_identificacion' => 'nullable|string|max:255',
            'identificacion' => 'nullable|string|max:255',
            'pais_id' => 'nullable|exists:paises,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'ciudad_id' => 'nullable|exists:ciudades,id',
            'direccion' => 'nullable|string|max:255',
            'telefono_fijo' => 'nullable|string|max:255',
            'telefono_movil' => 'nullable|string|max:255',
            'nivel' => 'required|exists:roles,id',
            'estado' => 'required|in:Activo,Inactivo',
        ];

        $validated = $request->validate($rules);

        // Subir o reemplazar foto
        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $validated['foto'] = $request->file('foto')->store('users', 'public');
        }

        // Si envían contraseña nueva
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        $role = Role::findById($validated['nivel'], 'web');
        $user->syncRoles([$role]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Mostrar formulario de cambio de contraseña
     */
    public function mostrarContrasena(): View
    {
        $user = Auth::user();
        return view('user.cambiar-contrasena', compact('user'));
    }

    /**
     * Cambiar contraseña de usuario
     */
    public function cambiarContrasena(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()
            ->route('home.index')
            ->with('success', 'Contraseña actualizada correctamente.');
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $hasTransactions = Venta::where('id_comprador', $user->id)->exists()
            || Compra::where('id_comprador', $user->id)->exists();

        if ($hasTransactions) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', 'Este usuario tiene compras o ventas registradas. No se puede eliminar.');
        }

        // Eliminar foto si existe
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
