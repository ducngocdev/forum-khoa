<x-app-layout>
  <x-slot name="header"><h2 class="text-xl font-semibold">Đặt câu hỏi</h2></x-slot>
  <div class="max-w-3xl mx-auto py-6 px-4">
    @if($errors->any())<div class="mb-3 text-red-700">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
    <form method="POST" action="{{ route('questions.store') }}" class="space-y-3">@csrf
      <div>
        <input id="title" name="title" value="{{ old('title') }}" required placeholder="Tiêu đề (ít nhất 10 ký tự)" class="w-full border rounded px-3 py-2">
        <div id="similar" class="mt-2 text-sm space-y-1"></div>
      </div>
      <select name="subject_id" required class="border rounded px-2 py-2">
        <option value="">Chọn môn học</option>
        @foreach($subjects as $s)<option value="{{ $s->id }}" @selected(old('subject_id') == $s->id)>{{ $s->code }} - {{ $s->name }}</option>@endforeach
      </select>
      <textarea name="body" rows="8" required placeholder="Mô tả chi tiết vấn đề của bạn (hỗ trợ Markdown, bọc code bằng ```)" class="w-full border rounded px-3 py-2">{{ old('body') }}</textarea>
      <input name="tags" value="{{ old('tags') }}" placeholder="Thẻ, cách nhau bằng dấu phẩy (tối đa 5)" class="w-full border rounded px-3 py-2">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Đăng câu hỏi</button>
    </form>
  </div>
  <script>
    const t = document.getElementById('title'), box = document.getElementById('similar'); let timer;
    t.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(async () => {
        const res = await fetch('{{ route('questions.similar') }}?title=' + encodeURIComponent(t.value), {headers: {Accept: 'application/json'}});
        const items = await res.json();
        box.innerHTML = items.length ? '<p class="font-semibold">Câu hỏi tương tự, có thể đã có người trả lời:</p>' : '';
        items.forEach(i => { const a = document.createElement('a'); a.href = i.url; a.target = '_blank'; a.textContent = i.title; a.className = 'block text-blue-700 underline'; box.appendChild(a); });
      }, 500);
    });
  </script>
</x-app-layout>
