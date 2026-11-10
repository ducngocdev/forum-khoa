<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Sách cũ trong Khoa</h2>
                <p class="text-sm text-blue-100 mt-1">Mua lại hoặc nhận sách được tặng từ thành viên khác.</p>
            </div>
            @auth
                <a href="{{ route('books.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm transition-colors hover:bg-blue-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Đăng sách
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-5">
        <form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row gap-3">
            <input name="q" value="{{ request('q') }}" placeholder="Tìm tên sách hoặc tác giả..." class="flex-1 rounded-lg border-gray-300 text-sm">
            <select name="type" class="rounded-lg border-gray-300 text-sm">
                <option value="">Tất cả</option>
                <option value="sale" @selected(request('type') === 'sale')>Đang bán</option>
                <option value="giveaway" @selected(request('type') === 'giveaway')>Cho tặng</option>
            </select>
            <button class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">Lọc</button>
        </form>

        @if(session('status') === 'book-created')
            <div class="rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">Đã đăng thông tin sách.</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($books as $book)
                <article class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col">
                    @if($book->image_path)
                        <img src="{{ asset('storage/' . $book->image_path) }}" alt="Ảnh sách {{ $book->title }}" class="mb-4 h-48 w-full rounded-lg object-cover">
                    @endif
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-bold text-gray-900">{{ $book->title }}</h3>
                        <span class="shrink-0 px-2 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                            {{ $book->type === 'giveaway' ? 'Cho tặng' : 'Bán' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">{{ $book->author ?: 'Chưa cập nhật tác giả' }}</p>
                    <p class="text-sm text-gray-700 mt-4 flex-1">{{ $book->description ?: 'Không có mô tả.' }}</p>
                    <div class="mt-4 pt-3 border-t border-gray-100 text-sm space-y-1">
                        <div><span class="text-gray-500">Tình trạng:</span> {{ ['new' => 'Mới', 'good' => 'Tốt', 'used' => 'Đã qua sử dụng'][$book->condition] }}</div>
                        <div class="font-bold text-blue-700">{{ $book->type === 'giveaway' ? 'Miễn phí' : number_format($book->price ?? 0) . ' đ' }}</div>
                        <div class="text-gray-500">Đăng bởi <a class="text-blue-600 hover:underline" href="{{ route('users.show', $book->user) }}">{{ $book->user->name }}</a></div>
                        @if($book->contact)<div class="text-gray-600">Liên hệ: {{ $book->contact }}</div>@endif
                    </div>
                </article>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-500">Chưa có sách phù hợp.</div>
            @endforelse
        </div>
        {{ $books->links() }}
    </div>
</x-app-layout>
