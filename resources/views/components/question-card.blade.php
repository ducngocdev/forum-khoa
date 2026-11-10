@props(['question'])

<article class="question-feed-card">
    <header class="question-feed-card__header">
        <x-user-avatar :user="$question->user" size="w-11 h-11" text-size="text-sm" />
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-1.5">
                <a href="{{ route('users.show', $question->user) }}" class="question-feed-card__author">{{ $question->user->name }}</a>
                <x-user-role-badge :user="$question->user" />
            </div>
            <div class="question-feed-card__meta">
                <span>{{ $question->created_at->diffForHumans() }}</span>
                @if($question->subject)
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('questions.index', ['subject' => $question->subject->id]) }}">{{ $question->subject->code }}</a>
                @endif
            </div>
        </div>
        <span class="question-feed-card__status {{ $question->post_type === 'share' ? 'is-share' : ($question->accepted_answer_id ? 'is-answered' : '') }}">
            {{ $question->post_type === 'share' ? 'Chia sẻ' : ($question->accepted_answer_id ? 'Đã giải đáp' : 'Thắc mắc') }}
        </span>
    </header>

    <div class="question-feed-card__content">
        <h3>
            <a href="{{ route('questions.show', $question) }}">{{ $question->title }}</a>
        </h3>
        <p>{{ \Illuminate\Support\Str::limit(strip_tags($question->body), 420) }}</p>
        @if($question->tags->isNotEmpty())
            <div class="post-tags">
                @foreach($question->tags as $tag)
                    <a href="{{ route('questions.index', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif
    </div>

    @if($question->relationLoaded('media') && $question->media->isNotEmpty())
        <div class="question-feed-card__media">
            @foreach($question->media->take(4) as $attachment)
                @if($attachment->is_video)
                    <video controls preload="metadata">
                        <source src="{{ asset('storage/' . $attachment->path) }}" type="{{ $attachment->mime_type }}">
                    </video>
                @else
                    <a href="{{ asset('storage/' . $attachment->path) }}" data-image-viewer data-image-download="{{ basename($attachment->path) }}" aria-label="Mở ảnh đính kèm">
                        <img src="{{ asset('storage/' . $attachment->path) }}" alt="Ảnh đính kèm của câu hỏi" loading="lazy">
                    </a>
                @endif
            @endforeach
            @if($question->media->count() > 4)
                <span class="question-feed-card__more-media">+{{ $question->media->count() - 4 }}</span>
            @endif
        </div>
    @endif

    <div class="question-feed-card__stats">
        @if($question->post_type === 'share')
            <span>Yes {{ $question->yes_votes_count ?? 0 }} · No {{ $question->no_votes_count ?? 0 }}</span>
            <span>{{ $question->comments_count ?? 0 }} bình luận · {{ $question->view_count }} lượt xem</span>
        @else
            <span>{{ $question->score }} phiếu</span>
            <span>{{ $question->answers_count }} câu trả lời · {{ $question->view_count }} lượt xem</span>
        @endif
    </div>

    <footer class="question-feed-card__actions">
        <div class="question-feed-card__vote">
            @include('forum.actions', [
                'type' => 'question',
                'model' => $question,
                'voteOnly' => true,
                'usefulnessOnly' => $question->post_type === 'share',
            ])
        </div>
        <a href="{{ route('questions.show', $question) }}{{ $question->post_type === 'share' ? '#post-comments' : '#answer-form' }}">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z"/>
            </svg>
            {{ $question->post_type === 'share' ? 'Bình luận' : 'Trả lời' }}
        </a>
        <a href="{{ route('questions.show', $question) }}">Xem thảo luận</a>
    </footer>
</article>
