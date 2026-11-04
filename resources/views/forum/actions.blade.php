{{-- Bình chọn + báo cáo. Biến: $type (question|answer|comment), $model --}}
<div class="flex items-center gap-4 text-sm">
  @if($type !== 'comment')
  <form method="POST" action="{{ route('votes.store') }}" class="flex items-center gap-1">@csrf
    <input type="hidden" name="type" value="{{ $type }}"><input type="hidden" name="id" value="{{ $model->id }}">
    <button name="value" value="1" title="Hữu ích" class="px-2 border rounded hover:bg-gray-100">▲</button>
    <span class="w-8 text-center font-semibold">{{ $model->score }}</span>
    <button name="value" value="-1" title="Chưa hữu ích" class="px-2 border rounded hover:bg-gray-100">▼</button>
  </form>
  @endif
  @auth
  <details>
    <summary class="cursor-pointer text-gray-500">Báo cáo</summary>
    <form method="POST" action="{{ route('reports.store') }}" class="mt-1 flex gap-1">@csrf
      <input type="hidden" name="type" value="{{ $type }}"><input type="hidden" name="id" value="{{ $model->id }}">
      <input name="reason" required minlength="5" placeholder="Lý do (spam, sai chủ đề, xúc phạm…)" class="border rounded px-2 py-1 text-sm">
      <button class="px-2 border rounded">Gửi</button>
    </form>
  </details>
  @endauth
</div>
