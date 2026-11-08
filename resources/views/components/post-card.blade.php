@props(['model', 'type', 'question' => null])

@php
    $isQuestion = $type === 'question';
    $isAccepted = !$isQuestion && $model->is_accepted;
@endphp

<article class="post-card {{ $isAccepted ? 'post-card--accepted' : '' }}">
    <div class="post-card__vote">
        @include('forum.actions', ['type' => $type, 'model' => $model, 'voteOnly' => true])
    </div>

    <div class="post-card__main">
        <div class="post-author">
            <x-user-meta :user="$model->user" :time="$model->created_at->diffForHumans()" />
        </div>

        @if($isAccepted)
            <div class="accepted-badge">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 12 4 4L19 6"/>
                </svg>
                Câu trả lời được người hỏi chấp nhận
            </div>
        @endif

        <div class="post-content">
            {!! \Illuminate\Support\Str::markdown($model->body, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
        </div>

        @if($isQuestion)
            <div class="post-tags">
                @foreach($model->tags as $tag)
                    <a href="{{ route('questions.index', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif

        <div class="post-footer">
            <div class="post-footer__actions">
                @if(auth()->id() === $model->user_id)
                    <form method="POST" action="{{ $isQuestion ? route('questions.destroy', $model) : route('answers.destroy', $model) }}" onsubmit="return confirm('Bạn có chắc muốn thu hồi nội dung này?')">
                        @csrf
                        @method('DELETE')
                        <button class="post-action post-action--danger">Thu hồi</button>
                    </form>
                @endif
                @if($type === 'answer')
                    <a href="#answer-form" class="post-action">Trả lời</a>
                @endif
                @include('forum.actions', ['type' => $type, 'model' => $model, 'voteOnly' => false, 'reportOnly' => $isQuestion])
                @if($type === 'answer' && auth()->id() === $question?->user_id)
                    <form method="POST" action="{{ route('answers.accept', $model) }}">
                        @csrf
                        <button class="post-action {{ $isAccepted ? 'post-action--success' : '' }}">
                            {{ $isAccepted ? 'Bỏ chấp nhận' : 'Chấp nhận đáp án' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @include('forum.comments', ['type' => $type, 'model' => $model])
    </div>
</article>
