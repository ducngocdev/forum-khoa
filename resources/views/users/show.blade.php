<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <div class="flex items-center gap-3">
      <x-user-avatar :user="$user" size="w-12 h-12" text-size="text-lg" class="shadow-sm" />
      <div>
        <div class="flex items-center gap-2">
          <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ $user->name }}</h2>
          @if($user->role === 'admin')
            <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700 border border-red-200">Quản trị</span>
          @elseif($user->role === 'moderator')
            <span class="px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">Kiểm duyệt viên</span>
          @endif
        </div>
        <p class="text-xs text-gray-500 mt-0.5">Tham gia từ {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</p>
        <p class="text-xs text-gray-600 mt-1">{{ collect([$user->academic_title, $user->class_name, $user->study_year])->filter()->join(' · ') }}</p>
      </div>
      </div>
      @auth
        @if(auth()->id() === $user->id)
          <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
            Chỉnh sửa hồ sơ
          </a>
        @endif
      @endauth
    </div>
  </x-slot>

  <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 space-y-6">
    @if(session('status') === 'profile-updated')
      <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
        Đã lưu thông tin hồ sơ thành công.
      </div>
    @endif

    <section class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
      <div class="flex items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-gray-900">Thông tin cá nhân</h3>
        @if(!$user->academic_title && !$user->class_name && !$user->study_year && !$user->bio)
          <span class="text-xs text-gray-500">Chưa cập nhật</span>
        @endif
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="rounded-lg bg-gray-50 p-3">
          <div class="text-xs text-gray-500">Vai trò / học vị</div>
          <div class="mt-1 text-sm font-semibold text-gray-900">{{ $user->academic_title ?: 'Chưa cập nhật' }}</div>
        </div>
        <div class="rounded-lg bg-gray-50 p-3">
          <div class="text-xs text-gray-500">Lớp / đơn vị</div>
          <div class="mt-1 text-sm font-semibold text-gray-900">{{ $user->class_name ?: 'Không áp dụng' }}</div>
        </div>
        <div class="rounded-lg bg-gray-50 p-3">
          <div class="text-xs text-gray-500">Kinh nghiệm</div>
          <div class="mt-1 text-sm font-semibold text-gray-900">{{ $user->study_year ?: 'Chưa cập nhật' }}</div>
        </div>
      </div>
      @if($user->bio)
        <div class="mt-4 pt-4 border-t border-gray-100">
          <div class="text-xs text-gray-500">Giới thiệu bản thân</div>
          <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $user->bio }}</p>
        </div>
      @endif
    </section>
    <!-- Hàng thống kê cá nhân -->
    <div class="grid sm:grid-cols-3 gap-4">
      <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
        </div>
        <div>
          <div class="text-xs text-gray-500 font-medium">Điểm uy tín</div>
          <div class="text-2xl font-bold text-gray-900">{{ number_format($user->reputation) }}</div>
        </div>
      </div>

      <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
          </svg>
        </div>
        <div>
          <div class="text-xs text-gray-500 font-medium">Câu trả lời</div>
          <div class="text-2xl font-bold text-gray-900">{{ number_format($answersCount) }}</div>
        </div>
      </div>

      <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <div class="text-xs text-gray-500 font-medium">Được chấp nhận</div>
          <div class="text-2xl font-bold text-gray-900">{{ number_format($acceptedAnswersCount) }}</div>
        </div>
      </div>
    </div>

    <!-- Câu hỏi gần đây -->
    <section class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
      <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Câu hỏi gần đây
      </h3>
      <div class="divide-y divide-gray-100">
        @forelse($questions as $question)
          <div class="py-3 flex items-center justify-between gap-4">
            <a href="{{ route('questions.show', $question) }}" class="text-sm font-semibold text-gray-800 hover:text-blue-600 transition-colors">
              {{ $question->title }}
            </a>
            <span class="text-xs text-gray-400 shrink-0">{{ $question->created_at->diffForHumans() }}</span>
          </div>
        @empty
          <p class="text-sm text-gray-500 py-3">Thành viên này chưa đăng câu hỏi nào.</p>
        @endforelse
      </div>
    </section>

    <section class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
      <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Lịch sử trả lời</h3>
      <div class="divide-y divide-gray-100">
        @forelse($answers as $answer)
          <div class="py-3 flex items-center justify-between gap-4">
            <a href="{{ route('questions.show', $answer->question) }}" class="text-sm font-semibold text-gray-800 hover:text-blue-600">{{ $answer->question->title }}</a>
            <span class="text-xs text-gray-400 shrink-0">{{ $answer->created_at->diffForHumans() }}</span>
          </div>
        @empty
          <p class="text-sm text-gray-500 py-3">Thành viên này chưa trả lời câu hỏi nào.</p>
        @endforelse
      </div>
    </section>

    <section class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-bold text-gray-900">Sách đang đăng</h3>
        <a href="{{ route('books.index') }}" class="text-sm text-blue-600 hover:underline">Xem chợ sách</a>
      </div>
      <div class="grid sm:grid-cols-2 gap-3">
        @forelse($books as $book)
          <div class="border border-gray-100 rounded-lg p-3">
            <div class="font-semibold text-sm text-gray-900">{{ $book->title }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $book->type === 'giveaway' ? 'Cho tặng' : number_format($book->price ?? 0) . ' đ' }}</div>
          </div>
        @empty
          <p class="text-sm text-gray-500">Chưa đăng sách nào.</p>
        @endforelse
      </div>
    </section>

    <!-- Lịch sử điểm uy tín -->
    <section class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
      <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
        </svg>
        Lịch sử điểm uy tín
      </h3>
      @php
        $labels = [
          'vote' => 'Được bình chọn',
          'accepted' => 'Câu trả lời được chấp nhận',
          'accept' => 'Chấp nhận câu trả lời',
          'unaccept' => 'Bị bỏ chấp nhận',
          'content_removed' => 'Nội dung bị ẩn do vi phạm',
        ];
      @endphp
      <div class="divide-y divide-gray-100">
        @forelse($logs as $log)
          <div class="flex justify-between items-center py-3">
            <div>
              <div class="text-sm font-medium text-gray-800">{{ $labels[$log->action] ?? $log->action }}</div>
              <div class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</div>
            </div>
            <div class="font-bold text-sm px-2.5 py-1 rounded-lg {{ $log->points > 0 ? 'bg-emerald-50 text-emerald-700' : ($log->points < 0 ? 'bg-red-50 text-red-700' : 'bg-gray-50 text-gray-600') }}">
              {{ $log->points > 0 ? '+' : '' }}{{ $log->points }}
            </div>
          </div>
        @empty
          <p class="text-sm text-gray-500 py-3">Chưa có lịch sử điểm uy tín.</p>
        @endforelse
      </div>
    </section>
  </div>
</x-app-layout>
