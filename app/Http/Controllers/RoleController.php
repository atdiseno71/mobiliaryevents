<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\DatatableExport;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{

    use DatatableExport;

    private string $route = 'roles';

    public function __construct()
    {
        $this->middleware("can:{$this->route}.index")->only('index');
        $this->middleware("can:{$this->route}.create")->only('create', 'store');
        $this->middleware("can:{$this->route}.edit")->only('edit', 'update');
        $this->middleware("can:{$this->route}.destroy")->only('destroy');
    }

    public function index(Request $request)
    {
        $perPage = (new Role)->getPerPage(); // items por página
        $query = Role::with('permissions');

        if ($request->ajax()) {
            $this->dtApplyLength($request, $perPage); // asegurar la paginación

            return DataTables::eloquent($query)
                ->addColumn('permissions', function ($row) {
                    return $row->permissions->count()
                        ? '<span class="badge bg-info text-dark">' . $row->permissions->count() . ' permisos</span>'
                        : '<span class="text-muted">---</span>';
                })
                ->addColumn('action', function ($row) {
                    $route = $this->route;
                    return view('partials.actions', compact('row', 'route'))->render();
                })
                ->rawColumns(['permissions', 'action'])
                ->toJson();
        }

        $roles = $query->orderBy('id', 'desc')->paginate($perPage);
        return view('roles.index', compact('roles', 'perPage'));
    }

    public function create()
    {
        $disabled = false;

        $permissions = Permission::orderBy('name')->get();
        $role = new Role();
        return view('roles.create', compact('role', 'permissions', 'disabled'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function show(int $id)
    {
        $disabled = true;

        $role = Role::findOrFail($id);

        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'disabled'));
    }

    public function edit(Role $role)
    {
        $disabled = false;

        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'disabled'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
