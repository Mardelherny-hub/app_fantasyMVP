<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = User::query()->with(['roles'])->orderByDesc('id');

        // Filtros básicos
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        if ($verified = $request->get('verified')) {
            $verified === 'yes'
                ? $query->whereNotNull('email_verified_at')
                : $query->whereNull('email_verified_at');
        }

        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::query()->orderBy('name')->pluck('name');

        return view('admin.users.index', compact('users','roles'));
    }

    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);
        $roles = Role::query()->orderBy('name')->pluck('name');
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request, string $locale)
    {
        app()->setLocale($locale);

        $data = $request->validated();

        $user = new User();
        $user->name     = $data['name'];
        $user->username = $data['username'] ?? null;
        $user->email    = $data['email'];
        $user->password = Hash::make($data['password']);
        if (($data['email_verified_at'] ?? false) === true) {
            $user->email_verified_at = now();
        }
        $user->save();

        $user->syncRoles($data['roles']);

        return redirect()
            ->route('admin.users.index', $locale)
            ->with('success', __('Usuario creado correctamente.'));
    }

    public function edit(Request $request, string $locale, User $user)
    {
        app()->setLocale($locale);
        $roles = Role::query()->orderBy('name')->pluck('name');
        $currentRoles = $user->roles->pluck('name')->all();

        return view('admin.users.edit', compact('user','roles','currentRoles'));
    }

    public function update(UpdateUserRequest $request, string $locale, User $user)
    {
        app()->setLocale($locale);
        $data = $request->validated();

        $user->name     = $data['name'];
        $user->username = $data['username'] ?? null;
        $user->email    = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Verificación de email
        if (array_key_exists('email_verified_at', $data)) {
            $user->email_verified_at = $data['email_verified_at'] ? now() : null;
        }

        $user->save();

        $user->syncRoles($data['roles']);

        return redirect()
            ->route('admin.users.index', $locale)
            ->with('success', __('Usuario actualizado correctamente.'));
    }

    public function destroy(Request $request, string $locale, User $user)
    {
        app()->setLocale($locale);
        $user->delete(); // SoftDeletes si el modelo lo usa
        return redirect()
            ->route('admin.users.index', $locale)
            ->with('success', __('Usuario eliminado.'));
    }

    public function toggle(Request $request, string $locale, $id)
    {
        app()->setLocale($locale);

        $user = \App\Models\User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            $user->restore();
            $msg = __('Usuario reactivado.');
        } else {
            $user->delete();
            $msg = __('Usuario desactivado.');
        }

        return redirect()
            ->route('admin.users.index', $locale)
            ->with('success', $msg);
    }

}
