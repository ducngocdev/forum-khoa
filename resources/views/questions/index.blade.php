<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 leading-tight">
                Danh sách câu hỏi
            </h2>
            @auth
                <a href="{{ route('questions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Đặt câu hỏi
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cột trái: 2/3 (Form tìm kiếm + Danh sách câu hỏi) -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Form tìm kiếm & Bộ lọc -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <form method="GET" action="{{ route('questions.index') }}" class="space-y-3">
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input name="q" value="{{ request('q') }}" placeholder="Tìm kiếm câu hỏi..."
                                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shrink-0">
                                Tìm kiếm
                            </button>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-gray-100 text-sm text-gray-600">
                            <select name="subject" class="text-xs border-gray-300 rounded-lg py-1.5 pl-2.5 pr-8 focus:ring-1 focus:ring-blue-500">
                                <option value="">Tất cả môn học</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}" @selected(request('subject') == $s->id)>{{ $s->code }} - {{ $s->name }}</option>
                                @endforeach
                            </select>

                            <select name="tag" class="text-xs border-gray-300 rounded-lg py-1.5 pl-2.5 pr-8 focus:ring-1 focus:ring-blue-500">
                                <option value="">Mọi thẻ</option>
                                @foreach($tags as $t)
                                    <option value="{{ $t->slug }}" @selected(request('tag') == $t->slug)>#{{ $t->name }}</option>
                                @endforeach
                            </select>

                            <select name="sort" class="text-xs border-gray-300 rounded-lg py-1.5 pl-2.5 pr-8 focus:ring-1 focus:ring-blue-500">
                                <option value="" @selected(request('sort') === null || request('sort') === '')>Mới nhất</option>
                                <option value="votes" @selected(request('sort') === 'votes')>Nhiều phiếu nhất</option>
                                <option value="views" @selected(request('sort') === 'views')>Nhiều lượt xem nhất</option>
                            </select>

                            <label class="inline-flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer ml-auto">
                                <input type="checkbox" name="filter" value="unanswered" @checked(request('filter') === 'unanswered')
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 text-xs">
                                <span>Chưa có đáp án</span>
                            </label>

                            @if(request()->hasAny(['q', 'subject', 'tag', 'sort', 'filter']))
                                <a href="{{ route('questions.index') }}" class="text-xs text-red-600 hover:underline">
                                    Xóa lọc
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Danh sách câu hỏi sử dụng question-card component -->
                <div class="space-y-3">
                    @forelse($questions as $q)
                        <x-question-card :question="$q" />
                    @empty
                        <div class="bg-white rounded-xl border border-gray-100 p-10 text-center shadow-sm">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-base font-semibold text-gray-900">Không tìm thấy câu hỏi nào</h3>
                            <p class="text-sm text-gray-500 mt-1">Hãy thử tìm với từ khóa khác hoặc đặt câu hỏi mới.</p>
                            @auth
                                <div class="mt-4">
                                    <a href="{{ route('questions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                                        Đặt câu hỏi ngay
                                    </a>
                                </div>
                            @endauth
                        </div>
                    @endforelse
                </div>

                <!-- Phân trang -->
                <div class="pt-2">
                    {{ $questions->links() }}
                </div>
            </div>

            <!-- Cột phải: 1/3 Sidebar (Môn học, Thẻ phổ biến, Top uy tín) -->
            <div class="space-y-6">
                <!-- Danh sách môn học (link lọc theo môn) -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center justify-between">
                        <span>Môn học</span>
                        @if(request('subject'))
                            <a href="{{ route('questions.index') }}" class="text-xs font-normal text-blue-600 hover:underline">Tất cả</a>
                        @endif
                    </h3>
                    <div class="space-y-1.5 max-h-64 overflow-y-auto pr-1">
                        @foreach($subjects as $sub)
                            <a href="{{ route('questions.index', ['subject' => $sub->id]) }}"
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs transition-colors {{ request('subject') == $sub->id ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                <span class="truncate pr-2">{{ $sub->code }} - {{ $sub->name }}</span>
                                <span class="shrink-0 text-gray-400">›</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Thẻ phổ biến (10 thẻ có nhiều câu hỏi nhất) -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">
                        Thẻ phổ biến
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($popularTags as $tag)
                            <a href="{{ route('questions.index', ['tag' => $tag->slug]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition-all {{ request('tag') == $tag->slug ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                                <span>#{{ $tag->name }}</span>
                                <span class="text-[10px] opacity-75">({{ $tag->questions_count }})</span>
                            </a>
                        @empty
                            <p class="text-xs text-gray-500">Chưa có thẻ nào.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top 5 uy tín -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">
                        Top 5 Uy tín
                    </h3>
                    <div class="divide-y divide-gray-100">
                        @forelse($topUsers as $index => $u)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                        {{ mb_substr($u->name ?? 'U', 0, 1) }}
                                    </div>
                                    <a href="{{ route('users.show', $u->id) }}" class="text-xs font-semibold text-gray-900 hover:text-blue-600 truncate block">
                                        {{ $u->name }}
                                    </a>
                                </div>
                                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 shrink-0">
                                    ★ {{ $u->reputation }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 py-1">Chưa có người dùng nổi bật.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
