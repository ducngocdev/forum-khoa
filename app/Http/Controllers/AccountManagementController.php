<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AcceptedAnswerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,active,locked'],
        ]);

        $users = User::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(($filters['status'] ?? 'all') === 'active', fn ($query) => $query->where('is_banned', false))
            ->when(($filters['status'] ?? 'all') === 'locked', fn ($query) => $query->where('is_banned', true))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $totalUsers = User::count();
        $lockedUsers = User::where('is_banned', true)->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'lockedUsers'));
    }

    public function lock(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);

        if ($request->user()->is($user)) {
            return back()->withErrors(['account' => 'Bạn không thể tự khóa tài khoản quản trị của mình.']);
        }

        if ($user->is_banned) {
            return back()->with('status', 'Tài khoản này hiện đã bị khóa.');
        }

        if ($user->role === 'admin' && ! User::where('role', 'admin')->where('is_banned', false)->whereKeyNot($user->id)->exists()) {
            return back()->withErrors(['account' => 'Không thể khóa quản trị viên hoạt động cuối cùng.']);
        }

        $user->update(['is_banned' => true]);

        return back()->with('status', "Đã khóa tài khoản {$user->name}.");
    }

    public function unlock(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);

        if (! $user->is_banned) {
            return back()->with('status', 'Tài khoản này hiện không bị khóa.');
        }

        $user->update(['is_banned' => false]);

        return back()->with('status', "Đã mở khóa tài khoản {$user->name}.");
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);

        if ($request->user()->is($user)) {
            return back()->withErrors(['account' => 'Bạn không thể tự xóa tài khoản quản trị của mình.']);
        }

        $deleted = DB::transaction(function () use ($user) {
            if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
                return false;
            }

            AcceptedAnswerService::clearForUser($user);
            $user->delete();

            return true;
        });

        if (! $deleted) {
            return back()->withErrors(['account' => 'Không thể xóa quản trị viên cuối cùng.']);
        }

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        return back()->with('status', "Đã xóa tài khoản {$user->name} cùng nội dung liên quan.");
    }
}
