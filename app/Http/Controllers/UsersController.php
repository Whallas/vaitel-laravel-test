<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\PermissionRegistrar;

class UsersController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'users'   => user()->account->users()
                ->whereKeyNot(user()->id)
                ->orderByName()
                ->filter(Request::only('search', 'role', 'trashed'))
                ->get()
                ->transform(fn ($user) => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'owner'      => $user->is_account_owner,
                    'photo'      => $user->photo_path ? URL::route('image', ['path' => $user->photo_path, 'w' => 40, 'h' => 40, 'fit' => 'crop']) : null,
                    'deleted_at' => $user->deleted_at,
                ]),
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return Inertia::render('Users/Create', [
            'permissions' => app(PermissionRegistrar::class)->getPermissions()->pluck('name'),
        ]);
    }

    public function store()
    {
        $this->authorize('create', User::class);

        Request::validate([
            'first_name'  => ['required', 'max:50'],
            'last_name'   => ['required', 'max:50'],
            'email'       => ['required', 'max:50', 'email', Rule::unique('users')],
            'password'    => ['nullable'],
            'role'        => ['required', 'in:account_owner,account_user'],
            'permissions' => [
                'array',
                'nullable',
                'exclude_if:role,account_owner',
                'exists:permissions,name',
            ],
            'photo'      => ['nullable', 'image'],
        ]);

        user()->account->users()->create([
            'first_name' => Request::get('first_name'),
            'last_name'  => Request::get('last_name'),
            'email'      => Request::get('email'),
            'password'   => Request::get('password'),
            'photo_path' => Request::file('photo') ? Request::file('photo')->store('users') : null,
        ])->syncRoles(Request::get('role'))
            ->syncPermissions(Request::get('permissions'));

        return Redirect::route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return Inertia::render('Users/Edit', [
            'user' => [
                'id'          => $user->id,
                'first_name'  => $user->first_name,
                'last_name'   => $user->last_name,
                'email'       => $user->email,
                'role'        => $user->getRoleNames()[0] ?? null,
                'permissions' => $user->getPermissionNames(),
                'photo'       => $user->photo_path ? URL::route('image', ['path' => $user->photo_path, 'w' => 60, 'h' => 60, 'fit' => 'crop']) : null,
                'deleted_at'  => $user->deleted_at,
            ],
            'permissions' => app(PermissionRegistrar::class)->getPermissions()->pluck('name'),
        ]);
    }

    public function update(User $user)
    {
        $this->authorize('update', $user);

        Request::validate([
            'first_name'  => ['required', 'max:50'],
            'last_name'   => ['required', 'max:50'],
            'email'       => ['required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password'    => ['nullable'],
            'role'        => ['required', 'in:account_owner,account_user'],
            'permissions' => [
                'array',
                'nullable',
                'exclude_if:role,account_owner',
                'exists:permissions,name',
            ],
            'photo'      => ['nullable', 'image'],
        ]);

        $user->update(Request::only('first_name', 'last_name', 'email', 'owner'));

        if (Request::file('photo')) {
            $user->update(['photo_path' => Request::file('photo')->store('users')]);
        }

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
        }

        $user->syncRoles(Request::get('role'))
            ->syncPermissions(Request::get('permissions'));

        return Redirect::back()->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return Redirect::back()->with('success', 'User deleted.');
    }

    public function restore(User $user)
    {
        $this->authorize('restore', $user);
        $user->restore();

        return Redirect::back()->with('success', 'User restored.');
    }
}
