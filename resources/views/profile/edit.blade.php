<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-3xl">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Sách đã đăng</h2>
                            <p class="mt-1 text-sm text-gray-600">Quản lý các sách bạn đang bán hoặc cho tặng.</p>
                        </div>
                        <a href="{{ route('books.create') }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold">Đăng sách</a>
                    </div>
                    <div class="mt-5 divide-y divide-gray-100 border-t border-gray-100">
                        @forelse($books as $book)
                            <div class="py-3 flex items-center justify-between gap-4">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $book->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $book->type === 'giveaway' ? 'Cho tặng' : 'Bán · ' . number_format($book->price ?? 0) . ' đ' }} · {{ $book->status === 'available' ? 'Đang hiển thị' : 'Đã bán' }}</div>
                                </div>
                                <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Xóa tin sách này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600 hover:underline">Xóa</button>
                                </form>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-gray-500">Bạn chưa đăng sách nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
