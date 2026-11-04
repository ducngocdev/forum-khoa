<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="max-w-3xl">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm text-blue-100 mb-3 tracking-wide">
                    Cộng đồng học thuật
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                    Hỏi đáp học thuật của Khoa
                </h1>
                <p class="mt-3 text-base sm:text-lg text-blue-100 leading-relaxed max-w-2xl">
                    Nơi sinh viên, giảng viên và cựu sinh viên cùng trao đổi kiến thức, giải đáp thắc mắc và chia sẻ tài liệu học tập một cách nhanh chóng, chính xác.
                </p>

                <!-- Form tìm kiếm lớn & Nút đặt câu hỏi -->
                <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <form action="{{ route('questions.index') }}" method="GET" class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="q" placeholder="Tìm kiếm câu hỏi, môn học, từ khóa..."
                               class="w-full pl-10 pr-4 py-3 bg-white text-gray-900 rounded-lg shadow-md border-0 focus:ring-2 focus:ring-blue-300 placeholder-gray-400 text-sm sm:text-base">
                    </form>

                    @auth
                        <a href="{{ route('questions.create') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg font-semibold text-sm sm:text-base text-blue-700 bg-white hover:bg-blue-50 shadow-md hover:shadow-lg transition-all duration-150">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Đặt câu hỏi
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg font-semibold text-sm sm:text-base text-blue-700 bg-white hover:bg-blue-50 shadow-md hover:shadow-lg transition-all duration-150">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Đặt câu hỏi
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">
        <!-- Hàng thẻ thống kê -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($totalQuestions) }}</div>
                    <div class="text-sm font-medium text-gray-500">Tổng câu hỏi</div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($answeredQuestions) }}</div>
                    <div class="text-sm font-medium text-gray-500">Đã có đáp án chấp nhận</div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
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

        <!-- Lưới thẻ môn học -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Môn học đào tạo</h2>
                    <p class="text-sm text-gray-500">Duyệt câu hỏi theo từng học phần của Khoa</p>
                </div>
                <a href="{{ route('questions.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    Xem tất cả &rarr;
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @forelse($subjects as $sub)
                    <a href="{{ route('questions.index', ['subject' => $sub->id]) }}"
                       class="bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 hover:-translate-y-0.5 transition-all duration-200 group flex flex-col justify-between">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                {{ $sub->code }}
                            </span>
                            <div class="mt-2 text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2" title="{{ $sub->name }}">
                                {{ $sub->name }}
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-gray-50 text-xs text-gray-500 flex items-center justify-between">
                            <span>Câu hỏi</span>
                            <span class="font-bold text-gray-700">{{ $sub->total_questions }}</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white rounded-xl p-6 text-center text-gray-500 text-sm">
                        Chưa có dữ liệu môn học.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Hai cột: Câu hỏi mới nhất (6 câu) và Người đóng góp nổi bật (5 người) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cột trái: Câu hỏi mới nhất (2/3) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Câu hỏi mới nhất</h2>
                        <p class="text-sm text-gray-500">Những thắc mắc vừa được đăng tải</p>
                    </div>
                    <a href="{{ route('questions.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        Xem thêm &rarr;
                    </a>
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
            </div>

            <!-- Cột phải: Người đóng góp nổi bật (1/3) -->
            <div class="space-y-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Đóng góp nổi bật</h2>
                    <p class="text-sm text-gray-500">Thành viên tích cực hỗ trợ giải đáp</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 divide-y divide-gray-100">
                    @forelse($topContributors as $index => $u)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="relative shrink-0">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-sm uppercase shadow-sm">
                                        {{ mb_substr($u->name ?? 'U', 0, 1) }}
                                    </div>
                                    @if($index < 3)
                                        <span class="absolute -top-1 -left-1 w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold text-white {{ $index === 0 ? 'bg-amber-500' : ($index === 1 ? 'bg-slate-400' : 'bg-amber-700') }}">
                                            {{ $index + 1 }}
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('users.show', $u->id) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 truncate block">
                                        {{ $u->name }}
                                    </a>
                                    <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                        <span>{{ $u->answers }} câu trả lời</span>
                                        <span>·</span>
                                        <span class="text-emerald-600 font-medium">{{ $u->accepted }} chấp nhận</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    ★ {{ $u->reputation }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-2">Chưa có người đóng góp.</p>
                    @endforelse
                </div>

                <!-- Thẻ banner giới thiệu nhỏ bên dưới danh sách top contributor -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-5 text-sm text-gray-700">
                    <div class="font-semibold text-blue-900 mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Quy tắc điểm uy tín
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Tham gia trả lời để tích lũy điểm uy tín (+10 khi được bình chọn hữu ích, +15 khi câu trả lời được chấp nhận).
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

