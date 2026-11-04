<x-app-layout>
  <x-slot name="header"><h2 class="text-xl font-semibold">Kiểm duyệt</h2></x-slot>
  <div class="max-w-4xl mx-auto py-6 px-4 space-y-3">
    @forelse($reports as $r)
      <div class="bg-white border rounded p-4 space-y-2">
        <div class="text-sm text-gray-500">{{ class_basename($r->reportable_type) }} · báo cáo bởi {{ $r->reporter->name }} · lý do: {{ $r->reason }}</div>
        @if($r->reportable)
          <div>{{ \Illuminate\Support\Str::limit($r->reportable->title ?? $r->reportable->body, 200) }}</div>
          <div class="text-sm text-gray-500">Tác giả: {{ $r->reportable->user->name }}</div>
        @else <div class="text-gray-500">Nội dung đã bị xóa.</div> @endif
        <div class="flex gap-2">
          <form method="POST" action="{{ route('moderation.hide', $r) }}">@csrf<button class="px-3 py-1 bg-red-600 text-white rounded">Ẩn nội dung (−20 điểm tác giả)</button></form>
          <form method="POST" action="{{ route('moderation.dismiss', $r) }}">@csrf<button class="px-3 py-1 border rounded">Bỏ qua</button></form>
        </div>
      </div>
    @empty <p class="text-gray-600">Không có báo cáo nào đang chờ xử lý.</p> @endforelse

    <section class="pt-4">
      <h3 class="text-lg font-semibold mb-3">Phiếu bầu nghi vấn (không được tính điểm uy tín)</h3>
      @forelse($suspicious as $v)
        @php($author = optional($v->votable)->user)
        <div class="bg-white border rounded p-4 text-sm text-gray-600">
          {{ $v->user->name }} {{ $v->value > 0 ? 'bầu lên' : 'bầu xuống' }}
          @if($author){{ $author->name }}@else tác giả không còn tồn tại @endif
          · {{ $v->updated_at->diffForHumans() }}
        </div>
      @empty
        <p class="text-gray-600">Không có phiếu bầu nghi vấn.</p>
      @endforelse
    </section>
  </div>
</x-app-layout>
