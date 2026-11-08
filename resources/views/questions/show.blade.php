<x-app-layout>
  <x-slot name="header">
    <div class="mx-auto flex max-w-[880px] items-center">
      <a href="{{ route('questions.index') }}" class="text-sm font-medium text-gray-500 transition hover:text-blue-600">
        <span aria-hidden="true">←</span> Quay lại danh sách
      </a>
    </div>
  </x-slot>

  <div class="mx-auto max-w-[880px] space-y-6 px-4 py-8 sm:px-6">
    @if($errors->any())
      <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">{{ $errors->first() }}</div>
    @endif
    @if(session('status'))
      <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">{{ session('status') }}</div>
    @endif

    <header class="space-y-3">
      <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
        @if($question->subject)
          <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
            {{ $question->subject->code }} · {{ $question->subject->name }}
          </span>
        @endif
        <span>{{ $question->view_count }} lượt xem</span>
      </div>
      <h1 class="text-2xl font-bold leading-tight text-gray-900">{{ $question->title }}</h1>
    </header>

    <x-post-card :model="$question" type="question" />

    <section class="space-y-4" aria-labelledby="answers-heading">
      <div class="flex items-center justify-between gap-3">
        <h2 id="answers-heading" class="text-xl font-bold text-gray-900">{{ $answers->count() }} câu trả lời</h2>
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
  </div>
</x-app-layout>
