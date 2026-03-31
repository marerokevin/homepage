<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()?->is_admin, 403);

        $users = User::orderBy('name')->get();

        return view('admin.users', compact('users'));
    }

    public function toggleVehicle(User $user)
    {
        abort_if(!auth()->user()?->is_admin, 403);
        $user->update(['can_book_vehicle' => !$user->can_book_vehicle]);
        return response()->json(['success' => true, 'can_book_vehicle' => $user->can_book_vehicle]);
    }

    public function toggleVehicleAdmin(User $user)
    {
        abort_if(!auth()->user()?->is_admin, 403);
        $user->update(['is_vehicle_admin' => !$user->is_vehicle_admin]);
        return response()->json(['success' => true, 'is_vehicle_admin' => $user->is_vehicle_admin]);
    }
}
