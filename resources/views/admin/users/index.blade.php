<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Quản lý tài khoản</h1>
                <p class="text-sm text-gray-500 mt-1">Xem, khóa, mở khóa hoặc xóa tài khoản thành viên.</p>
            </div>
            <span class="text-sm text-gray-500">Chỉ quản trị viên</span>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">
        @if(session('status'))
            <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif
        @if($errors->has('account'))
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ $errors->first('account') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</div>
                <div class="text-sm text-gray-500">Tổng tài khoản</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="text-2xl font-bold text-emerald-600">{{ number_format($totalUsers - $lockedUsers) }}</div>
                <div class="text-sm text-gray-500">Đang hoạt động</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="text-2xl font-bold text-amber-600">{{ number_format($lockedUsers) }}</div>
                <div class="text-sm text-gray-500">Đã khóa</div>
            </div>
        </div>

        <section class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <form method="GET" action="{{ route('admin.users.index') }}" class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-3">
                <label class="sr-only" for="user-search">Tìm theo tên hoặc email</label>
                <input id="user-search" type="search" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên hoặc email..."
                       class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <label class="sr-only" for="user-status">Lọc theo trạng thái</label>
                <select id="user-status" name="status" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all" @selected(request('status', 'all') === 'all')>Tất cả trạng thái</option>
                    <option value="active" @selected(request('status') === 'active')>Đang hoạt động</option>
                    <option value="locked" @selected(request('status') === 'locked')>Đã khóa</option>
                </select>
                <button class="inline-flex justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Lọc tài khoản</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Thành viên</th>
                            <th scope="col" class="px-4 py-3">Vai trò</th>
                            <th scope="col" class="px-4 py-3">Điểm uy tín</th>
                            <th scope="col" class="px-4 py-3">Trạng thái</th>
                            <th scope="col" class="px-4 py-3">Ngày tham gia</th>
                            <th scope="col" class="px-4 py-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="align-middle">
                                <td class="px-4 py-3">
                                    <a href="{{ route('users.show', $user) }}" class="font-semibold text-gray-900 hover:text-blue-600">{{ $user->name }}</a>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">ID: {{ $user->id }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @php($roleLabels = ['admin' => 'Quản trị viên', 'moderator' => 'Kiểm duyệt viên', 'student' => 'Sinh viên'])
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $user->isAdmin() ? 'bg-violet-50 text-violet-700' : ($user->role === 'moderator' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ $roleLabels[$user->role] ?? $user->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($user->reputation) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_banned ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ $user->is_banned ? 'Đã khóa' : 'Hoạt động' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->id() === $user->id)
                                            <span class="text-xs text-gray-400 self-center">Tài khoản của bạn</span>
                                        @else
                                            @if($user->is_banned)
                                                <form method="POST" action="{{ route('admin.users.unlock', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">Mở khóa</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.lock', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="rounded-lg border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50">Khóa</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Xóa vĩnh viễn tài khoản này cùng câu hỏi, câu trả lời và nội dung liên quan?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Xóa</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-500">Không tìm thấy tài khoản phù hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="border-t border-gray-100 p-4">{{ $users->links() }}</div>
            @endif
        </section>
    </div>
</x-app-layout>
