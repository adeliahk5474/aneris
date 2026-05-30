<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function artists(Request $request)
    {
        $search = $request->input('search');

        $artists = User::where('role', 'artist')
            ->withCount(['artworks', 'commissionServices'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(20);

        return view('admin.users.artists', compact('artists', 'search'));
    }

    public function clients(Request $request)
    {
        $search = $request->input('search');

        $clients = User::where('role', 'client')
            ->withCount('ordersAsClient')
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(20);

        return view('admin.users.clients', compact('clients', 'search'));
    }
}
