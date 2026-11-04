<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center gap-2">
      <a href="{{ route('questions.index') }}" class="text-sm text-blue-600 hover:underline flex items-center">
        &larr; Quay lại danh sách
      </a>
    </div>
  </x-slot>

  <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 space-y-6">
    @if($errors->any())
      <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium">
        {{ $errors->first() }}
      </div>
    @endif
    @if(session('status'))
      <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium">
        {{ session('status') }}
      </div>
    @endif

    <!-- Khối câu hỏi chính -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 space-y-4">
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2">
          @if($question->subject)
            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700">
              {{ $question->subject->code }} - {{ $question->subject->name }}
            </span>
          @endif
          <span class="text-xs text-gray-500">{{ $question->view_count }} lượt xem</span>
        </div>
      </div>

      <h1 class="text-2xl font-bold text-gray-900 leading-snug">
        {{ $question->title }}
      </h1>

      <div class="flex items-center gap-2 text-xs text-gray-500 pb-3 border-b border-gray-100 flex-wrap">
        <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[11px] uppercase">
          {{ mb_substr($question->user->name ?? 'U', 0, 1) }}
        </div>
        <a href="{{ route('users.show', $question->user) }}" class="font-semibold text-gray-800 hover:text-blue-600">
          {{ $question->user->name }}
        </a>
        @if($question->user->role === 'admin')
          <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">Quản trị</span>
        @elseif($question->user->role === 'moderator')
          <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">Kiểm duyệt viên</span>
        @endif
        <span class="text-gray-400">({{ $question->user->reputation }} điểm)</span>
        <span class="text-gray-400">·</span>
        <span>Đăng {{ $question->created_at->diffForHumans() }}</span>
      </div>

      <div class="md text-gray-800 leading-relaxed text-base pt-1">
        {!! \Illuminate\Support\Str::markdown($question->body, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
      </div>

      <div class="flex flex-wrap gap-1.5 pt-2">
        @foreach($question->tags as $t)
          <a href="{{ route('questions.index', ['tag' => $t->slug]) }}" class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium hover:bg-blue-100 transition-colors">
            #{{ $t->name }}
          </a>
        @endforeach
      </div>

      <div class="pt-3 border-t border-gray-100">
        @include('forum.actions', ['type' => 'question', 'model' => $question])
        @include('forum.comments', ['type' => 'question', 'model' => $question])
      </div>
    </div>

    <!-- Danh sách câu trả lời -->
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900">
          {{ $answers->count() }} câu trả lời
        </h3>
      </div>

      @forelse($answers as $a)
        <div class="bg-white border rounded-xl shadow-sm p-6 space-y-4 transition-all {{ $a->is_accepted ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-100' }}">
          @if($a->is_accepted)
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Câu trả lời được người hỏi chấp nhận
            </div>
          @endif

          <div class="md text-gray-800 leading-relaxed text-base">
            {!! \Illuminate\Support\Str::markdown($a->body, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
          </div>

          <div class="flex items-center justify-between gap-4 pt-3 border-t border-gray-100 flex-wrap">
            <div class="flex items-center gap-2 text-xs text-gray-500">
              <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[11px] uppercase">
                {{ mb_substr($a->user->name ?? 'U', 0, 1) }}
              </div>
              <a href="{{ route('users.show', $a->user) }}" class="font-semibold text-gray-800 hover:text-blue-600">
                {{ $a->user->name }}
              </a>
              @if($a->user->role === 'admin')
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">Quản trị</span>
              @elseif($a->user->role === 'moderator')
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">Kiểm duyệt viên</span>
              @endif
              <span class="text-gray-400">({{ $a->user->reputation }} điểm)</span>
              <span class="text-gray-400">·</span>
              <span>Trả lời {{ $a->created_at->diffForHumans() }}</span>
            </div>

            <div class="flex items-center gap-3">
              @include('forum.actions', ['type' => 'answer', 'model' => $a])
              @if(auth()->id() === $question->user_id)
                <form method="POST" action="{{ route('answers.accept', $a) }}">
                  @csrf
                  <button class="text-xs px-3 py-1.5 border rounded-lg font-medium transition-colors {{ $a->is_accepted ? 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100' : 'bg-gray-50 text-gray-700 border-gray-300 hover:bg-gray-100' }}">
                    {{ $a->is_accepted ? 'Bỏ chấp nhận' : '✓ Chấp nhận đáp án' }}
                  </button>
                </form>
              @endif
            </div>
          </div>

          @include('forum.comments', ['type' => 'answer', 'model' => $a])
        </div>
      @empty
        <div class="bg-white border border-gray-100 rounded-xl p-8 text-center text-gray-500 text-sm shadow-sm">
          Chưa có câu trả lời nào cho câu hỏi này. Hãy là người đầu tiên giúp đỡ bạn học!
        </div>
      @endforelse
    </div>

    <!-- Form gửi câu trả lời -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 space-y-4">
      <h3 class="text-lg font-bold text-gray-900">Câu trả lời của bạn</h3>
      @auth
        <form method="POST" action="{{ route('answers.store', $question) }}" class="space-y-3">
          @csrf
          <textarea name="body" rows="6" required minlength="10"
                    placeholder="Viết câu trả lời chi tiết, dẫn chứng hoặc giải pháp của bạn (hỗ trợ định dạng Markdown)..."
                    class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
          <div class="flex justify-between items-center">
            <span class="text-xs text-gray-400">Tối thiểu 10 ký tự. Sử dụng Markdown để trình bày mã nguồn hoặc công thức.</span>
            <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm transition-colors">
              Gửi câu trả lời
            </button>
          </div>
        </form>
      @else
        <div class="text-center py-6 bg-gray-50 rounded-xl border border-gray-200">
          <p class="text-sm text-gray-600 mb-3">Bạn cần đăng nhập để tham gia trả lời câu hỏi này.</p>
          <div class="flex justify-center gap-3">
            <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
              Đăng nhập
            </a>
            @if(Route::has('register'))
              <a href="{{ route('register') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Đăng ký tài khoản
              </a>
            @endif
          </div>
        </div>
      @endauth
    </div>
  </div>
</x-app-layout>
