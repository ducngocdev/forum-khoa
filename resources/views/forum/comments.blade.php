{{-- Biến: $type (question|answer), $model --}}
<section class="comments-panel" aria-label="Bình luận" @if($type === 'question' && $model->post_type === 'share') id="post-comments" @endif>
  @if($model->comments->isNotEmpty())
    <div class="comments-toolbar">
      <button type="button" class="comments-toggle comments-toggle--collapse" data-comments-collapse>
        Thu gọn bình luận
      </button>
      @if($model->comments->count() > 3)
        <button type="button" class="comments-toggle" data-comments-toggle aria-expanded="false">
          Xem {{ $model->comments->count() - 3 }} bình luận khác
        </button>
      @endif
    </div>
  @endif
  @foreach($model->comments as $commentIndex => $c)
    <div class="comment-item {{ $commentIndex >= 3 ? 'comment-item--hidden' : '' }}" data-comment-item>
      <div class="comment-item__body">{{ $c->body }}</div>
      <div class="comment-item__meta">
        <x-user-meta :user="$c->user" :time="$c->created_at->diffForHumans()" />
        <div class="comment-item__actions">
          @include('forum.actions', ['type' => 'comment', 'model' => $c, 'voteOnly' => false])
          @if(auth()->id() === $c->user_id)
            <form method="POST" action="{{ route('comments.destroy', $c) }}" onsubmit="return confirm('Bạn có chắc muốn thu hồi bình luận này?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="post-action post-action--danger">Thu hồi</button>
            </form>
          @endif
        </div>
      </div>
    </div>
  @endforeach
  @auth
    <button type="button" class="add-comment-link" data-comment-toggle>Thêm bình luận</button>
    <form method="POST" action="{{ route('comments.store') }}" class="comment-form" data-comment-form hidden>@csrf
      <input type="hidden" name="type" value="{{ $type }}"><input type="hidden" name="id" value="{{ $model->id }}">
      <input name="body" required minlength="3" placeholder="Viết bình luận..." aria-label="Nội dung bình luận">
      <button type="submit">Gửi</button>
    </form>
  @endauth
</section>

@once
  <script>
    document.addEventListener('click', function (event) {
      const trigger = event.target.closest('[data-comment-toggle]');
      if (trigger) {
        const form = trigger.parentElement.querySelector('[data-comment-form]');
        form.hidden = !form.hidden;
        if (!form.hidden) form.querySelector('input[name="body"]').focus();
        return;
      }

      const commentsToggle = event.target.closest('[data-comments-toggle]');
      if (commentsToggle) {
        const panel = commentsToggle.closest('.comments-panel');
        panel.querySelectorAll('[data-comment-item]').forEach((item) => item.classList.remove('comment-item--hidden'));
        commentsToggle.hidden = true;
        const collapse = panel.querySelector('[data-comments-collapse]');
        if (collapse) collapse.hidden = false;
        commentsToggle.setAttribute('aria-expanded', 'true');
        return;
      }
    });

    document.addEventListener('click', function (event) {
      const collapse = event.target.closest('[data-comments-collapse]');
      if (!collapse) return;
      const panel = collapse.closest('.comments-panel');
      const items = panel.querySelectorAll('[data-comment-item]');
      const isCollapsed = panel.classList.toggle('comments-panel--collapsed');
      items.forEach((item, index) => {
        item.classList.toggle('comment-item--hidden', isCollapsed || index >= 3);
      });
      const expand = panel.querySelector('[data-comments-toggle]');
      if (expand) expand.hidden = isCollapsed;
      collapse.textContent = isCollapsed ? 'Xem bình luận' : 'Thu gọn bình luận';
      collapse.setAttribute('aria-expanded', String(!isCollapsed));
    });
  </script>
@endonce
