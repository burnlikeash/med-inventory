<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (
            $user->isAdministrator()
            && $validated['role'] !== User::ROLE_ADMINISTRATOR
            && User::where('role', User::ROLE_ADMINISTRATOR)->count() <= 1
        ) {
            return back()
                ->withInput()
                ->with('error', 'The last Administrator cannot be assigned a different role.');
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if (
            $user->isAdministrator()
            && User::where('role', User::ROLE_ADMINISTRATOR)->count() <= 1
        ) {
            return back()->with('error', 'The last Administrator account cannot be deleted.');
        }

        if (
            $user->equipmentMovements()->exists()
            || $user->approvedDisposals()->exists()
            || $user->auditLogs()->exists()
        ) {
            return back()->with(
                'error',
                'This user cannot be deleted because the account is referenced by system history. Keep the account so historical records remain attributable.'
            );
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
