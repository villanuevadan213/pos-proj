<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index() {
        // Fetch all users from the database
        $users = User::all();

        // Return the view with the users data
        return view('users.index', compact('users'));
    }

    public function create() {
        return view('users.create');
    }

    public function show(User $user) {
        return view('users.show', ['user' => $user]);
    }
    
    public function store() {
        request()->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required'],
            'role' => ['required', 'in:user,admin'],
        ]);

        User::create([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'email' => request('email'),
            'password' => Hash::make('password'),
            'role' => request('role'),
        ]);
        return redirect('/users');
    }

    public function update(User $user) {
        request()->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['nullable', Password::default(), 'confirmed'],
            'role' => ['required', 'in:user,admin'],
        ]);

        $updateData = [
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'email' => request('email'),
            'role' => request('role'),
        ];

        // Only update the password if it's provided
        if (request('password')) {
            $updateData['password'] = Hash::make(request('password'));
        }

        $user->update($updateData);

        return redirect('/users')->with('success', 'User updated successfully.');
    }

    public function edit(User $user) {
        return view('users.edit', ['user' => $user]);
    }

    public function destroy(User $user) {
        $authUser = Auth::user();
    
        // Disallow self-deletion (optional, recommended for safety)
        if ($authUser->id === $user->id) {
            return redirect('/users')->with('error', 'You cannot delete your own account.');
        }
    
        // Role-based permission check
        if ($authUser->role === 'owner') {
            // Owner can delete admin or user (not another owner for safety)
            if (in_array($user->role, ['admin', 'user'])) {
                $user->delete();
                return redirect('/users')->with('success', 'User deleted successfully.');
            }
        } elseif ($authUser->role === 'admin') {
            // Admin can only delete users
            if ($user->role === 'user') {
                $user->delete();
                return redirect('/users')->with('success', 'User deleted successfully.');
            }
        }
    
        // Default: deny access
        return redirect('/users')->with('error', 'You are not authorized to delete this user.');
    }

}
