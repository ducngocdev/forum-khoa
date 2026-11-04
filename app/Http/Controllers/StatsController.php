<?php
namespace App\Http\Controllers;

use App\Models\{Subject, User};
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $bySubject = Subject::withCount([
            'questions' => fn ($q) => $q->where('is_hidden', false),
            'questions as answered_count' => fn ($q) => $q->where('is_hidden', false)->whereNotNull('accepted_answer_id'),
        ])->orderByDesc('questions_count')->get();
        $topRep = User::orderByDesc('reputation')->limit(10)->get();
        $topMonth = User::withCount(['answers as accepted_month' => fn ($q) =>
            $q->where('is_accepted', true)->where('updated_at', '>=', now()->startOfMonth())])
            ->orderByDesc('accepted_month')->limit(10)->get();
        $top = DB::table('v_top_contributors')->orderByDesc('accepted')->orderByDesc('answers')->limit(10)->get();
        return view('stats.index', compact('bySubject', 'topRep', 'topMonth', 'top'));
    }
}
