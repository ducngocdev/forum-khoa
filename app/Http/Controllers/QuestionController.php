<?php
namespace App\Http\Controllers;

use App\Models\{Question, Subject, Tag};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    private const FT = 'MATCH(title, body) AGAINST (? IN NATURAL LANGUAGE MODE)';

    public function index(Request $r)
    {
        $q = Question::with(['user', 'subject', 'tags'])->withCount('answers')->where('is_hidden', false);
        if ($s = trim((string) $r->q)) $q->whereRaw(self::FT, [$s])->orderByRaw(self::FT . ' DESC', [$s]);
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
            'topUsers' => \Illuminate\Support\Facades\DB::table('v_top_contributors')->orderByDesc('reputation')->limit(5)->get(),
        ]);
    }

    public function create() { return view('questions.create', ['subjects' => Subject::orderBy('name')->get()]); }

    public function store(Request $r)
    {
        $d = $r->validate([
            'title' => 'required|string|min:10|max:200', 'body' => 'required|string|min:20',
            'subject_id' => 'required|exists:subjects,id', 'tags' => 'nullable|string|max:200',
        ]);
        $question = Question::create([
            'user_id' => $r->user()->id, 'subject_id' => $d['subject_id'], 'title' => $d['title'], 'body' => $d['body'],
        ]);
        $ids = collect(explode(',', $d['tags'] ?? ''))->map(fn ($t) => trim($t))->filter()->unique()->take(5)
            ->map(fn ($name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id);
        $question->tags()->sync($ids);
        return redirect()->route('questions.show', $question);
    }

    public function show(Question $question)
    {
        abort_if($question->is_hidden, 404);
        $question->increment('view_count');
        $question->load(['user', 'subject', 'tags']);
        $answers = $question->answers()->where('is_hidden', false)->with('user')
            ->orderByDesc('is_accepted')->orderByDesc('score')->get();
        $visible = fn ($c) => $c->where('is_hidden', false)->with('user');
        $question->load(['comments' => $visible]);
        $answers->load(['comments' => $visible]);
        return view('questions.show', compact('question', 'answers'));
    }

    // Gợi ý câu hỏi tương tự khi đang gõ tiêu đề
    public function similar(Request $r)
    {
        $t = trim((string) $r->query('title', ''));
        if (mb_strlen($t) < 4) return response()->json([]);
        $rows = Question::where('is_hidden', false)->whereRaw(self::FT, [$t])->limit(5)->get(['id', 'title']);
        return response()->json($rows->map(fn ($q) => ['title' => $q->title, 'url' => route('questions.show', $q)]));
    }
}
