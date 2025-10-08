<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = Role::query()->where('guard_name','web')->orderBy('name');

        if ($search = $request->get('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($perm = $request->get('perm')) {
            $query->whereHas('permissions', fn($q) => $q->where('name', $perm));
        }

        $roles = $query->with('permissions')->paginate(15)->withQueryString();
        $permissions = Permission::query()->where('guard_name','web')->orderBy('name')->pluck('name');

        return view('admin.roles.index', compact('roles','permissions'));
    }

    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);
        $permissions = Permission::query()->where('guard_name','web')->orderBy('name')->pluck('name');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request, string $locale)
    {
        app()->setLocale($locale);
        $data = $request->validated();

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index', $locale)
            ->with('success', __('Rol creado correctamente.'));
    }

    public function edit(Request $request, string $locale, Role $role)
    {
        app()->setLocale($locale);
        abort_unless($role->guard_name === 'web', 404);

        $permissions = Permission::query()->where('guard_name','web')->orderBy('name')->pluck('name');
        $currentPermissions = $role->permissions->pluck('name')->all();

        return view('admin.roles.edit', compact('role','permissions','currentPermissions'));
    }

    public function update(UpdateRoleRequest $request, string $locale, Role $role)
    {
        app()->setLocale($locale);
        abort_unless($role->guard_name === 'web', 404);

        $data = $request->validated();

        $role->name = $data['name'];
        $role->save();

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index', $locale)
            ->with('success', __('Rol actualizado correctamente.'));
    }

    public function destroy(Request $request, string $locale, Role $role)
    {
        app()->setLocale($locale);
        abort_unless($role->guard_name === 'web', 404);

        // Prevención básica: evitar borrar el rol admin si es el único admin existente
        if ($role->name === 'admin') {
            return back()->with('error', __('No se puede eliminar el rol admin.'));
        }

        $role->delete();

        return redirect()->route('admin.roles.index', $locale)
            ->with('success', __('Rol eliminado.'));
    }
}
