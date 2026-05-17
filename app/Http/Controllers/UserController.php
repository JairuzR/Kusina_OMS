<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        $stats = [
            'total'     => User::count(),
            'active'    => User::where('status', 'active')->count(),
            'inactive'  => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
        ];

        return view('users.index', compact('users', 'roles', 'stats'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
            'phone'    => 'nullable|string|max:20',
            'status'   => 'required|in:active,inactive,suspended',
            'avatar'   => 'nullable|image|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($validated);
        $user->assignRole($validated['role']);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'created',
            'module'      => 'users',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => 'Created user ' . $user->name,
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('roles');
        $recentLogs = AuditLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('users.show', compact('user', 'recentLogs'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
            'phone'    => 'nullable|string|max:20',
            'status'   => 'required|in:active,inactive,suspended',
            'avatar'   => 'nullable|image|max:2048',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);
        $user->syncRoles([$validated['role']]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'updated',
            'module'      => 'users',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => 'Updated user ' . $user->name,
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own status.');
        }

        $user->update(['status' => $request->status]);

        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->update(['status' => 'inactive']);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deactivated and removed.');
    }

    public function impersonate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        session(['impersonating' => auth()->id()]);
        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Now viewing as ' . $user->name);
    }

    public function stopImpersonating()
    {
        $originalId = session('impersonating');

        if (!$originalId) {
            return redirect()->route('dashboard');
        }

        $originalUser = User::find($originalId);
        session()->forget('impersonating');
        auth()->login($originalUser);

        return redirect()->route('users.index')
            ->with('success', 'Returned to your account.');
    }

    public function forceLogout(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot force logout yourself.');
        }

        // Delete all sessions for this user
        \DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'force_logout',
            'module'      => 'users',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => 'Force logged out user ' . $user->name,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);

        return back()->with('success', $user->name . ' has been logged out from all devices.');
    }
}