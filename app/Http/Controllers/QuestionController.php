<?php
namespace App\Http\Controllers;

use App\Models\{Question, Subject, Tag};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class QuestionController extends Controller
{
    private const FT = 'MATCH(title, body) AGAINST (? IN NATURAL LANGUAGE MODE)';

    public function index(Request $r)
    {
        $q = Question::with(['user', 'subject', 'tags', 'media'])->withCount([
            'answers',
            'comments',
            'votes as yes_votes_count' => fn ($votes) => $votes->where('value', 1),
            'votes as no_votes_count' => fn ($votes) => $votes->where('value', -1),
        ])->where('is_hidden', false);
        if ($r->filled('post_type') && in_array($r->post_type, ['question', 'share'], true)) {
            $q->where('post_type', $r->post_type);
        }
        if ($s = trim((string) $r->q)) {
            if ($q->getModel()->getConnection()->getDriverName() === 'mysql') {
                $q->whereRaw(self::FT, [$s])->orderByRaw(self::FT . ' DESC', [$s]);
            } else {
                $term = '%' . $s . '%';
                $q->where(fn ($query) => $query->where('title', 'like', $term)->orWhere('body', 'like', $term))
                    ->latest();
            }
        }
        else {
            match ($r->sort) {
                'votes' => $q->orderByDesc('score'),
                'views' => $q->orderByDesc('view_count'),
                default => $q->latest(),
            };
        }
        if ($r->subject) $q->where('subject_id', $r->subject);
        if ($r->tag) $q->whereHas('tags', fn ($t) => $t->where('slug', $r->tag));
        if ($r->filter === 'unanswered') $q->whereNull('accepted_answer_id');
        return view('questions.index', [
            'questions' => $q->paginate(10)->withQueryString(),
            'subjects' => Subject::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'popularTags' => Tag::withCount('questions')->orderByDesc('questions_count')->limit(10)->get(),
            'topUsers' => \Illuminate\Support\Facades\DB::table('v_top_contributors')
                ->leftJoin('users as contributor', 'contributor.id', '=', 'v_top_contributors.id')
                ->select('v_top_contributors.*', 'contributor.avatar_path')
                ->orderByDesc('v_top_contributors.reputation')
                ->limit(5)
                ->get(),
        ]);
    }

    public function create() { return view('questions.create', ['subjects' => Subject::orderBy('name')->get()]); }

    public function store(Request $r)
    {
        $d = $r->validate([
            'post_type' => 'sometimes|in:question,share',
            'title' => 'required|string|min:10|max:200', 'body' => 'required|string|min:20',
            'subject_id' => 'nullable|exists:subjects,id', 'tags' => 'nullable|string|max:200',
            'attachments' => 'nullable|array|max:6',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov|max:51200',
        ]);

        $attachments = $r->file('attachments', []);
        foreach ($attachments as $file) {
            if (str_starts_with((string) $file->getMimeType(), 'image/') && $file->getSize() > 5 * 1024 * 1024) {
                throw ValidationException::withMessages([
                    'attachments' => 'Mỗi ảnh được chọn không vượt quá 5 MB.',
                ]);
            }
        }

        $storedPaths = [];
        try {
            $question = DB::transaction(function () use ($r, $d, $attachments, &$storedPaths) {
                $question = Question::create([
                    'user_id' => $r->user()->id,
                    'subject_id' => $d['subject_id'] ?? null,
                    'post_type' => $d['post_type'] ?? 'question',
                    'title' => $d['title'],
                    'body' => $d['body'],
                ]);
                $ids = collect(explode(',', $d['tags'] ?? ''))->map(fn ($t) => trim($t))->filter()->unique()->take(5)
                    ->map(fn ($name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id);
                $question->tags()->sync($ids);

                foreach ($attachments as $file) {
                    $path = $file->store('questions/' . $question->id, 'public');
                    if ($path === false) {
                        throw new RuntimeException('Không thể lưu tệp đính kèm.');
                    }
                    $storedPaths[] = $path;
                    $question->media()->create([
                        'path' => $path,
                        'mime_type' => $file->getMimeType(),
                    ]);
                }

                return $question;
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }

        return redirect()->route('questions.show', $question);
    }

    public function show(Question $question)
    {
        abort_if($question->is_hidden, 404);
        $question->increment('view_count');
        $question->load(['user', 'subject', 'tags', 'media']);
        $answers = $question->answers()->where('is_hidden', false)->with('user')
            ->orderByRaw('CASE WHEN id = ? THEN 1 ELSE 0 END DESC', [$question->accepted_answer_id ?? 0])
            ->orderByDesc('score')->get();
        $visible = fn ($c) => $c->where('is_hidden', false)->with('user');
        $question->load(['comments' => $visible]);
        $answers->load(['comments' => $visible]);
        return view('questions.show', compact('question', 'answers'));
    }

    public function destroy(Request $r, Question $question)
    {
        abort_unless($question->user_id === $r->user()->id, 403);
        $question->update(['is_hidden' => true]);
        return redirect()->route('questions.index')->with('status', 'Đã thu hồi bài viết.');
    }

    // Gợi ý câu hỏi tương tự khi đang gõ tiêu đề
    public function similar(Request $r)
    {
        $t = trim((string) $r->query('title', ''));
        if (mb_strlen($t) < 4) return response()->json([]);
        $query = Question::where('is_hidden', false);
        if (Question::query()->getModel()->getConnection()->getDriverName() === 'mysql') {
            $query->whereRaw(self::FT, [$t]);
        } else {
            $term = '%' . $t . '%';
            $query->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('body', 'like', $term));
        }
        $rows = $query->limit(5)->get(['id', 'title']);
        return response()->json($rows->map(fn ($q) => ['title' => $q->title, 'url' => route('questions.show', $q)]));
    }
}
