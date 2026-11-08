@props(['voteOnly' => false, 'reportOnly' => false])

@if(!$reportOnly && $type === 'question')
  <form method="POST" action="{{ route('votes.store') }}" class="{{ $voteOnly ? 'vote-stack' : 'vote-inline' }}">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="id" value="{{ $model->id }}">
    <button name="value" value="1" aria-label="Tăng điểm câu hỏi" class="vote-button">
      <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 15 7-7 7 7"/></svg>
    </button>
    <span class="vote-score">{{ $model->score }}</span>
    <button name="value" value="-1" aria-label="Giảm điểm câu hỏi" class="vote-button">
      <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
    </button>
  </form>
@elseif(!$reportOnly && !$voteOnly)
  @php
    $isStudent = auth()->check() && (strtolower((string) auth()->user()->academic_title) === 'sinh viên' || (!auth()->user()->academic_title && auth()->user()->role === 'student'));
    $yesVotes = $model->votes()->with('user')->where('value', 1)->get();
    $noVotes = $model->votes()->with('user')->where('value', -1)->get();
  @endphp
  <div class="usefulness">
    <span class="usefulness__label">Hữu ích?</span>
    <form method="POST" action="{{ route('votes.store') }}" class="usefulness__form">
      @csrf
      <input type="hidden" name="type" value="{{ $type }}"><input type="hidden" name="id" value="{{ $model->id }}">
      <button name="value" value="1" class="usefulness__button usefulness__button--yes" @disabled($isStudent || !auth()->check()) aria-label="Đánh giá Yes">
        <span aria-hidden="true">👍</span> Yes <strong>{{ $yesVotes->count() }}</strong>
      </button>
      <button name="value" value="-1" class="usefulness__button usefulness__button--no" @disabled($isStudent || !auth()->check()) aria-label="Đánh giá No">
        <span aria-hidden="true">👎</span> No <strong>{{ $noVotes->count() }}</strong>
      </button>
    </form>
    <details class="reviewers">
      <summary>Xem người đã đánh giá</summary>
      <div class="reviewers__panel">
        <div><strong>Yes:</strong> @forelse($yesVotes as $vote)<a href="{{ route('users.show', $vote->user) }}">{{ $vote->user->name }}</a>{{ !$loop->last ? ', ' : '' }}@empty Chưa có @endforelse</div>
        <div><strong>No:</strong> @forelse($noVotes as $vote)<a href="{{ route('users.show', $vote->user) }}">{{ $vote->user->name }}</a>{{ !$loop->last ? ', ' : '' }}@empty Chưa có @endforelse</div>
      </div>
    </details>
  </div>
@endif

@if((!$voteOnly || $reportOnly) && auth()->check())
  <details class="report-action">
    <summary>Báo cáo</summary>
    <form method="POST" action="{{ route('reports.store') }}">@csrf
      <input type="hidden" name="type" value="{{ $type }}"><input type="hidden" name="id" value="{{ $model->id }}">
      <input name="reason" required minlength="5" placeholder="Lý do báo cáo">
      <button>Gửi</button>
    </form>
  </details>
@endif
