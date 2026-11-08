@props(['question'])

<div class="bg-white border border-gray-100 rounded-xl p-4 sm:p-5 flex gap-4 sm:gap-5 shadow-sm hover:shadow-md transition-shadow duration-200">
    <!-- Cột thống kê số phiếu và số trả lời -->
    <div class="flex flex-col gap-2 shrink-0 items-center">
        <!-- Ô số phiếu -->
        <div class="w-14 h-14 rounded-lg bg-gray-50 border border-gray-200 flex flex-col items-center justify-center text-center">
            <span class="text-base font-bold text-gray-700 leading-none">{{ $question->score }}</span>
            <span class="text-[11px] text-gray-500 mt-0.5">phiếu</span>
        </div>

        <!-- Ô số trả lời: nếu có accepted_answer_id thì nền xanh lá chữ trắng -->
        @if($question->accepted_answer_id)
            <div class="w-14 h-14 rounded-lg bg-emerald-600 border border-emerald-600 flex flex-col items-center justify-center text-center text-white shadow-sm" title="Đã có câu trả lời được chấp nhận">
                <span class="text-base font-bold leading-none">{{ $question->answers_count }}</span>
                <span class="text-[11px] leading-tight mt-0.5 font-medium">trả lời ✓</span>
            </div>
        @else
            <div class="w-14 h-14 rounded-lg bg-gray-50 border border-gray-200 flex flex-col items-center justify-center text-center {{ $question->answers_count > 0 ? 'border-blue-200 bg-blue-50/50' : '' }}">
                <span class="text-base font-bold {{ $question->answers_count > 0 ? 'text-blue-600' : 'text-gray-600' }} leading-none">{{ $question->answers_count }}</span>
                <span class="text-[11px] text-gray-500 mt-0.5">trả lời</span>
            </div>
        @endif
    </div>

    <!-- Nội dung chính câu hỏi -->
    <div class="flex-1 min-w-0 flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                @if($question->subject)
                    <a href="{{ route('questions.index', ['subject' => $question->subject->id]) }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                        {{ $question->subject->code }}
                    </a>
                @endif
                <span class="text-xs text-gray-400">·</span>
                <span class="text-xs text-gray-500">{{ $question->view_count }} lượt xem</span>
            </div>

            <h3 class="text-base sm:text-lg font-semibold leading-snug">
                <a href="{{ route('questions.show', $question) }}" class="text-gray-900 hover:text-blue-600 transition-colors line-clamp-2">
                    {{ $question->title }}
                </a>
            </h3>
        </div>

        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
            <!-- Danh sách thẻ tags -->
            <div class="flex flex-wrap gap-1.5">
                @forelse($question->tags as $t)
                    <a href="{{ route('questions.index', ['tag' => $t->slug]) }}" class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                        #{{ $t->name }}
                    </a>
                @empty
                @endforelse
            </div>

            <!-- Tác giả & thời gian -->
            <x-user-meta :user="$question->user" :time="$question->created_at->diffForHumans()" class="ml-auto" />
        </div>
    </div>
</div>
