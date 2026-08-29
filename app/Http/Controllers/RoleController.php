<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    private const MODULE_LABELS = [
        'users' => 'Usuarios',
        'roles' => 'Roles',
        'sellers' => 'Vendedores',
        'clients' => 'Clientes',
        'suppliers' => 'Proveedores',
        'products' => 'Productos',
        'raw_materials' => 'Materias primas',
        'bill_of_materials' => 'Lista de materiales',
        'production' => 'Producción',
        'inventory' => 'Inventario',
        'sales' => 'Ventas',
        'finances' => 'Finanzas',
        'reports' => 'Reportes',
        'dashboard' => 'Dashboard',
    ];

    public function index(): View
    {
        $roles = Role::withCount('permissions')->paginate(10);

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        $moduleLabels = self::MODULE_LABELS;
        $rolePermissions = [];

        return view('roles.create', compact('permissions', 'moduleLabels', 'rolePermissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create($request->validated());
        $role->permissions()->sync($request->validated('permissions', []));

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        $moduleLabels = self::MODULE_LABELS;
        $rolePermissions = $role->permissions()->pluck('permissions.id')->all();

        return view('roles.edit', compact('role', 'permissions', 'moduleLabels', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->validated());
        $role->permissions()->sync($request->validated('permissions', []));

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        if ($role->users()->exists()) {
            return back()->with('error', 'No se puede eliminar un rol con usuarios asignados.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}