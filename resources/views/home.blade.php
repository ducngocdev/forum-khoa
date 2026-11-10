<x-app-layout>
    <!-- Hero Section -->
    <div class="home-hero bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white shadow-inner">
        <div class="home-hero__content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            <div class="max-w-3xl">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm text-blue-100 mb-3 tracking-wide">
                    Cộng đồng học thuật
                </span>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight">
                    Bạn hỏi tôi trả lời
                </h1>
                <p class="mt-2 text-sm sm:text-base text-blue-100 leading-relaxed max-w-2xl">
                    Cùng trao đổi kiến thức, giải đáp thắc mắc và chia sẻ tài liệu học tập.
                </p>

                <!-- Form tìm kiếm lớn & Nút đặt câu hỏi -->
                <div class="mt-5 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <form action="{{ route('questions.index') }}" method="GET" class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="q" placeholder="Tìm kiếm câu hỏi, môn học, từ khóa..."
                               class="w-full pl-10 pr-4 py-2.5 bg-white text-gray-900 rounded-lg shadow-md border-0 focus:ring-2 focus:ring-blue-300 placeholder-gray-400 text-sm sm:text-base">
                    </form>

                    @auth
                        <a href="{{ route('questions.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-lg font-semibold text-sm sm:text-base text-blue-700 bg-white hover:bg-blue-50 shadow-md hover:shadow-lg transition-all duration-150">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Đăng bài
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-lg font-semibold text-sm sm:text-base text-blue-700 bg-white hover:bg-blue-50 shadow-md hover:shadow-lg transition-all duration-150">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Đăng bài
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-7">
        <!-- Hàng thẻ thống kê -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($totalQuestions) }}</div>
                    <div class="text-sm font-medium text-gray-500">Tổng bài viết</div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($answeredQuestions) }}</div>
                    <div class="text-sm font-medium text-gray-500">Đã có đáp án chấp nhận</div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($totalMembers) }}</div>
                    <div class="text-sm font-medium text-gray-500">Thành viên tham gia</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:items-start">
            <div class="md:col-span-2 space-y-4">
                <section class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Lọc câu hỏi theo thẻ</h2>
                            <p class="text-xs text-gray-500 mt-1">Chọn môn học hoặc chủ đề bạn quan tâm.</p>
                        </div>
                        <a href="{{ route('questions.index') }}" class="text-xs font-medium text-blue-600 hover:underline shrink-0">Làm thêm</a>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @forelse($popularTags as $tag)
                            <a href="{{ route('questions.index', ['tag' => $tag->slug]) }}" class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-medium hover:bg-blue-100">
                                #{{ $tag->name }} <span class="text-blue-400">({{ $tag->questions_count }})</span>
                            </a>
                        @empty
                            <span class="text-sm text-gray-500">Chưa có thẻ nào.</span>
                        @endforelse
                    </div>
                </section>

                <section class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Câu hỏi mới nhất</h2>
                            <p class="text-xs text-gray-500">Những thắc mắc vừa được đăng tải</p>
                        </div>
                        <a href="{{ route('questions.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">Xem thêm &rarr;</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($latestQuestions as $q)
                            <x-question-card :question="$q" />
                        @empty
                            <div class="bg-white rounded-xl p-8 text-center text-gray-500 border border-gray-100">
                                Chưa có câu hỏi nào. Hãy là người đầu tiên đặt câu hỏi!
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-4">
                <section>
                    <h2 class="text-base font-bold text-gray-900">Đóng góp nổi bật</h2>
                    <p class="text-xs text-gray-500 mb-3">Thành viên tích cực hỗ trợ giải đáp</p>
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 divide-y divide-gray-100">
                        @forelse($topContributors as $index => $u)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="relative shrink-0">
                                        <x-user-avatar :user="$u" size="w-8 h-8" text-size="text-xs" />
                                        @if($index < 3)
                                            <span class="absolute -top-1 -left-1 w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold text-white {{ $index === 0 ? 'bg-amber-500' : ($index === 1 ? 'bg-slate-400' : 'bg-amber-700') }}">
                                                {{ $index + 1 }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('users.show', $u->id) }}" class="text-xs font-semibold text-gray-900 hover:text-blue-600 truncate block">{{ $u->name }}</a>
                                        <div class="text-[11px] text-gray-500 mt-0.5">
                                            {{ $u->answers }} câu trả lời · <span class="text-emerald-600">{{ $u->accepted }} chấp nhận</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">★ {{ $u->display_reputation }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm py-2">Chưa có người đóng góp.</p>
                        @endforelse
                    </div>
                </section>

                <section class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-xs text-gray-700">
                    <h3 class="font-semibold text-blue-900 mb-1">Quy tắc điểm uy tín</h3>
                    <p class="leading-relaxed">Tham gia trả lời để tích lũy điểm uy tín (+10 khi được bình chọn hữu ích, +15 khi câu trả lời được chấp nhận).</p>
                </section>

                <section>
                    <div class="mb-3">
                        <h2 class="text-base font-bold text-gray-900">Môn học đào tạo</h2>
                        <p class="text-xs text-gray-500">Duyệt câu hỏi theo từng học phần của Khoa</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        @forelse($subjects->take(4) as $sub)
                            <a href="{{ route('questions.index', ['subject' => $sub->id]) }}" class="bg-white rounded-lg p-3 border border-gray-100 shadow-sm hover:border-blue-200 hover:shadow transition group">
                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">{{ $sub->code }}</span>
                                <div class="mt-2 text-xs font-semibold text-gray-900 group-hover:text-blue-600 line-clamp-2" title="{{ $sub->name }}">{{ $sub->name }}</div>
                                <div class="mt-2 text-[10px] text-gray-500">Câu hỏi <span class="font-bold text-gray-700">{{ $sub->total_questions }}</span></div>
                            </a>
                        @empty
                            <div class="col-span-2 bg-white rounded-lg p-4 text-center text-gray-500 text-xs">Chưa có dữ liệu môn học.</div>
                        @endforelse
                    </div>
                    <a href="{{ route('questions.index') }}" class="inline-block mt-2 text-xs font-medium text-blue-600 hover:underline">Xem câu hỏi theo tất cả môn học &rarr;</a>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
