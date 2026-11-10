<x-app-layout>
  <x-slot name="header">
    <div class="mx-auto flex max-w-[880px] items-center">
      <a href="{{ route('questions.index') }}" data-history-back class="text-sm font-medium text-gray-500 transition hover:text-blue-600">
        <span aria-hidden="true">←</span> Quay lại danh sách
      </a>
    </div>
  </x-slot>

  <div class="question-detail mx-auto max-w-[920px] space-y-6 px-4 py-8 sm:px-6">
    @if($errors->any())
      <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">{{ $errors->first() }}</div>
    @endif
    @if(session('status'))
      <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">{{ session('status') }}</div>
    @endif

    <header class="question-detail__heading">
      <span class="question-detail__eyebrow"><span aria-hidden="true">✦</span> {{ $question->post_type === 'share' ? 'Bài chia sẻ' : 'Thắc mắc trong khoa' }}</span>
      <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
        @if($question->subject)
          <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
            {{ $question->subject->code }} · {{ $question->subject->name }}
          </span>
        @else
          <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
            Chưa phân loại
          </span>
        @endif
        <span>{{ $question->view_count }} lượt xem</span>
      </div>
      <h1 class="text-2xl font-extrabold leading-tight text-gray-900 sm:text-3xl">{{ $question->title }}</h1>
      <p class="text-sm text-gray-500">
        {{ $question->post_type === 'share' ? 'Một bài chia sẻ từ cộng đồng — hãy để lại bình luận để cùng trao đổi.' : 'Một thắc mắc từ cộng đồng — cùng trao đổi và chia sẻ cách giải.' }}
      </p>
    </header>

    <x-post-card :model="$question" type="question" />

    @if($question->post_type !== 'share')
      <section class="question-answer-feed space-y-4" aria-labelledby="answers-heading">
        <div class="question-feed-divider">
          <h2 id="answers-heading">{{ $answers->count() }} câu trả lời</h2>
          <span>Chia sẻ kiến thức, giữ cuộc trò chuyện thân thiện.</span>
        </div>

        @forelse($answers as $a)
          <x-post-card :model="$a" type="answer" :question="$question" />
        @empty
          <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
            Chưa có câu trả lời nào cho câu hỏi này. Hãy là người đầu tiên giúp đỡ bạn học!
          </div>
        @endforelse
      </section>

      <section id="answer-form" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-lg font-bold text-gray-900">Câu trả lời của bạn</h2>
        @auth
          <form method="POST" action="{{ route('answers.store', $question) }}" class="mt-4 space-y-3">
            @csrf
            <textarea name="body" rows="6" required minlength="10"
                      placeholder="Viết câu trả lời chi tiết, dẫn chứng hoặc giải pháp của bạn (hỗ trợ định dạng Markdown)..."
                      class="w-full rounded-lg border border-gray-200 p-3 text-sm leading-6 transition placeholder:text-gray-400 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-100"></textarea>
            <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
              <span class="text-xs text-gray-500">Tối thiểu 10 ký tự · Hỗ trợ Markdown</span>
              <button class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100">
                Đăng câu trả lời
              </button>
            </div>
          </form>
        @else
          <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-6 text-center">
            <p class="mb-3 text-sm text-gray-600">Bạn cần đăng nhập để tham gia trả lời câu hỏi này.</p>
            <div class="flex justify-center gap-3">
              <a href="{{ route('login') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Đăng nhập</a>
              @if(Route::has('register'))
                <a href="{{ route('register') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Đăng ký tài khoản</a>
              @endif
            </div>
          </div>
        @endauth
      </section>
    @endif
  </div>
</x-app-layout>
