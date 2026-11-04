{{-- Biến: $type (question|answer), $model --}}
<div class="mt-3 ml-4 border-l pl-3 space-y-2 text-sm">
  @foreach($model->comments as $c)
    <div>
      <span>{{ $c->body }}</span>
      <span class="text-gray-500">— {{ $c->user->name }}, {{ $c->created_at->diffForHumans() }}</span>
      @include('forum.actions', ['type' => 'comment', 'model' => $c])
    </div>
  @endforeach
  @auth
  <form method="POST" action="{{ route('comments.store') }}" class="flex gap-1">@csrf
    <input type="hidden" name="type" value="{{ $type }}"><input type="hidden" name="id" value="{{ $model->id }}">
    <input name="body" required minlength="3" placeholder="Thêm bình luận" class="flex-1 border rounded px-2 py-1">
    <button class="px-2 border rounded">Gửi</button>
  </form>
  @endauth
</div>
